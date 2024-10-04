---
title: Getting my daily news from a dot matrix printer
slug: getting-my-daily-news-from-a-dot-matrix-printer
description: Instead of doom-scrolling through my phone in the morning, I built an alternative with a Raspberry Pi, a dot matrix printer, and some PHP. 
categories: php, experiments
published_at: Oct 4 2024
excerpt: For a while now I've started my day by unlocking my phone and scrolling through different news and social media sites to see what's going on. It's not exactly great for my mental health and I've been trying to cut down on screen time for a while. I still want to stay up-to-date though, especially after I get up in the morning. What about a custom "front page" printed out and ready for me, instead.
---

For a while now I've started my day by unlocking my phone and scrolling through different news and social media sites to see what's going on. It's not exactly _great_ for my mental health and I've been trying to cut down on screen time for a while. I still want to stay up-to-date though, especially after I get up in the morning.

I recently purchased a dot matrix printer from eBay, and thought it would be a great excuse to have a custom "front page" printed out and ready for me each day. So, that's what I built!

Printer ASMR noises in the video below 👇

<video width="270" height="360" controls title="Dotmatrix Printing Example" style="margin:0px auto">
  <source src="/storage/images/blog/dotmatrix_example_2_bt709.mp4" type="video/mp4">
</video>

I'll take this article to dive in and show you what I used, how I set it up, and the **PHP script** that powers it all.

> Interested in that full source code? Check it out on the [GitHub repo](https://github.com/aschmelyun/dotmatrix-daily-news)!

## Purchasing the hardware

The supply list for this project was pretty small, and with the exception of the printer, most of this can be found on Amazon or other online retailers.

- Dot matrix printer
- Raspberry Pi Zero W [[link](https://vilros.com/products/raspberry-pi-zero-w-basic-starter-kit-1)]
- Serial to USB adapter [[link](https://www.amazon.com/dp/B00IDU0T1Y?ref=ppx_yo2ov_dt_b_fed_asin_title&th=1)]
- Power supply

The printer I purchased was a [Star NP-10](https://www.computerhistory.org/collections/catalog/102666267) from what looks like the mid-80's. I can't be 100% sure, but any dot matrix printer with a serial port should do the trick. The prices range from about $80-120 USD, but I was able to get this one for about half that price because it was marked as "unsure if working".

It did need a little cleaning up and some tuning of the ink ribbon cartridge (isn't that cool, it's like a typewriter!), but after that it fired right up and ran through the test page print.

After that, I hooked everything up. The Raspberry Pi is connected to my WiFi, and then via USB to the serial port of the printer. After turning on the printer and `ssh`ing into the Pi, I can verify that the printer is available at `/dev/usb/lp0`.

Now, **how do I get this thing to print?**

## Figuring out the printer's code

Because the printer is available at `lp0` I wanted to see if I could just echo raw text to it and have it come through the printer. So I ran the following:

```bash
echo "Hello, world!" > /dev/usb/lp0
```

Which resulted in an error that the file couldn't be accessed. Bummer, a permissions issue. Easily fixed though with some `chmod`'ing:

```bash
sudo chmod 666 /dev/usb/lp0
```

There might be a better way to handle that, but it allowed my echo to go through, and I saw the text available on the printer! Alright, I can send raw data to the printer via this file, so let's find a way to scale this up.

I use PHP as my language of choice in a day-to-day basis, and this is no exception. I write a basic script that accesses the file through `fopen()` and starts writing text to it. I try a few sentences, some spacing, and then some unicode art, but quickly find out that there's not as much character support on the printer as I was sending.

![Picture of a printed sheet showing a bunch of wrongly-encoded characters](/storage/images/blog/dotmatrix-encoding-errors.jpg)

So I thought it was about time that I start digging into how this thing _actually works_. Thanks to the hard work and dedication of internet hoarders, I found a [full manual for the printer](https://www.minuszerodegrees.net/manuals/Star%20Micronics/dot_matrix/Star%20Micronics%20-%20NP-10%20-%20Users%20Manual.pdf) scanned and uploaded as a PDF. 

Come to find out, either because of the age or just the manufacturing decision, this printer has a **very specific character set** that it accepts. Loosely based off of the IBM PC's [Code Page 437](https://en.wikipedia.org/wiki/Code_page_437) it consists mostly of your standard alpha-numeric characters, but with a small set of special symbols, lines, and boxes. Neat!

Sending these characters to the printer is pretty straightforward, I can just echo out the hex values with PHP like so:

```php
$horizontalDouble = "\xCD";
$deg = "\xF8";

echo str_repeat($horizontalDouble, 24);
echo '78' . $deg . 'F' . PHP_EOL;
```

Alright, so I'm able to write text to the printer just fine, and include some special characters and design symbols. Now I need to figure out _what_ I want to see every morning.

## Gathering the data

I knew I wanted four distinct sections for my personal front page: **weather, stocks, major news headlines, and a few top reddit posts**. After all, that's usually what I end up look at on my phone in the morning.

Additionally, since this is an experimental project, I wanted to remain super cheap for this data, free if at all possible. Thankfully there's an amazing [GitHub repo](https://github.com/public-apis/public-apis) for free and public APIs, so I just went through there and found the ones I needed.

- The weather pulls from [Open-Meteo](https://open-meteo.com/en/docs) and no API key is needed
- Stocks data pulls from [twelvedata](https://twelvedata.com/docs) that offers a generous free tier
- News headlines pull from [NYTimes](https://developer.nytimes.com/get-started) which has a decent free tier, good enough for this project
- Reddit posts pull from [Reddit JSON](https://www.reddit.com/r/redditdev/comments/rvqirc/how_to_get_reddit_api_data_using_json/) which is free (but I had to spoof my User-Agent)

For each of the sections, I wrote some basic PHP code to pull in the payload from the API endpoint and compile the data I wanted into a larger overall array. I only wanted specific stocks, types of headlines, and subreddit posts, and if any of the sections couldn't have data to present I just simply crash the script early so I can start it again at a later time.

This can be seen in this snippet which I use for pulling news headlines:

```php
// Get news headlines data
echo "Fetching news headlines data..." . PHP_EOL;
$newsUrl = NEWS . "?api-key=" . NEWSKEY;
$newsData = [];
$newsAmount = 0;

$data = json_decode(file_get_contents($newsUrl), true);

if (!isset($data['results'])) {
    die("Unable to retrieve news data");
}

foreach ($data['results'] as $article) {
    if (
        ($article['type'] === 'Article') &&
        (in_array($article['section'], ['U.S.', 'World', 'Weather', 'Arts'])) &&
        ($newsAmount < MAXNEWS)
    ) {
        $newsData[] = $article;
        $newsAmount++;
    }
}
```

The `NEWS`, `NEWSKEY`, and `MAXNEWS` variables are all constants instantiated at the top of the script for easy editing.

Running this compiles everything I want to see displayed on the paper, but now I need to take on the actual task of formatting everything for the printer, and sending it the raw data.

## Printing out my front page

I could just print out a heading for each section, but that's a little boring. I wanted a bit of **_flair_** to the project, so I decided to have a box at the top displaying the current date, day of the week, and my front page name all nicely bordered.

It took a little math, but I got everything working by using a combination of the hex values I talked about above, `str_repeat` and the knowledge that the page width for this printer is **80 characters**. 

Now, just simply go through each section, print a little heading:

```php
str_repeat($horizontalSingle, 3) . " WEATHER " . str_repeat($horizontalSingle, (PAGEWIDTH - 9)) . "\n";
```

And then print out the data that I want displayed for that section:

```php
"   " . round(($weatherData['daily']['daylight_duration'][0] / 3600), 2) . "h of Sunlight  -  Sunrise: " . date('g:ia', strtotime($weatherData['daily']['sunrise'][0])) . "  -  Sunset: " . date('g:ia', strtotime($weatherData['daily']['sunset'][0])) . "\n";
```

For the weather and stocks, I knew I wouldn't hit the edge of the paper so I just wrote everything in single long lines. But that's different for the news headlines and Reddit posts.

If I just feed the printer one long line of text, it's smart enough to cut it and start printing on another line. But, I didn't want a word getting cut off in the middle and starting on the next line. So I implemented a small function to handle line length and instead return back an array of lines with a max length corresponding to the page width.

```php
function splitString($string, $maxLength = PAGEWIDTH) {
    $result = [];
    $words = explode(' ', $string);
    $currentLine = '';

    foreach ($words as $word) {
        if (strlen($currentLine . $word) <= $maxLength) {
            $currentLine .= ($currentLine ? ' ' : '') . $word;
        } else {
            if ($currentLine) {
                $result[] = $currentLine;
                $currentLine = $word;
            } else {
                // If a single word is longer than maxLength, split it
                $result[] = substr($word, 0, $maxLength);
                $currentLine = substr($word, $maxLength);
            }
        }
    }

    if ($currentLine) {
        $result[] = $currentLine;
    }

    return $result;
}
```

Then I can just use it like so:

```php
foreach (splitString($redditPost) as $line) {
    fwrite($printer, $line) . "\n";
}
```

Now all that's left to do is run the script!

## Usage and wrapping up

I can fire off the printer manually by just running `php print.php` but I've instead set up a cron job to handle it for me.

Every morning at around 8am it starts printing out my personalized front page. I rip it off and check it out in the morning while drinking my coffee. 

![Example page printed](/storage/images/blog/dotmatrix-example-print.jpg)

As silly as it might sound, it just feels better having that finite amount of news on a single sheet of paper. Of being able to stop there instead of endlessly scrolling through websites and social media apps. 

Also, this was a super fun project and I'm hoping I can find more uses for this dot matrix printer. Working with physical hardware (especially older specimens like this) is always a blast for me, and being able to integrate them with new technology or use them in interesting ways just ignites pure passion and reinforces why I became a programmer in the first place.

So, what do you think? If you have any ideas for projects like this, or just have a question or comment, I'd love to hear it! Catch me on [Twitter](https://twitter.com/aschmelyun) if you'd like to chat more.
