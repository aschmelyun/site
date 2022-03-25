---
view: layout.post
title: I built a receipt printer for GitHub issues
description: I wanted a way to have physical tickets from GitHub in front of me. One Epson printer, a Raspberry Pi, and some PHP later, I had a working system in place.
categories: php, experiments
published: Mar 25 2022
excerpt: I have a lot of side projects on GitHub. Some of them are kind of popular, and I tend to get issues posted from time to time. The problem though is that usually they kind of get lost in the mix. I've been occasionally writing new issues down on sticky notes whenever I see a notification for one pop-up, but I always wanted an excuse to streamline it a bit more. 
---

I have a lot of [side projects](https://github.com/aschmelyun/repos) on GitHub. Some of them are kind of popular, and I tend to get issues posted from time to time. The problem though is that usually they kind of get lost in the mix of my emails, or I forget to go through my repos and add new items to my todo list.

I've been occasionally writing new issues down on sticky notes whenever I see a notification for an issue, but I always wanted an excuse to streamline the process a bit more. After seeing a receipt printer spitting out orders while grabbing some take-out the other day, I wondered if I could use one to print out a ticket each time an issue was added to one of my repos.

Spoiler alert, it worked!

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">So here&#39;s why I bought a receipt printer:<br><br>Every time one of my GitHub repos gets a new issue, I now get a physical ticket printed out on my desk 🪄 <a href="https://t.co/g6uYtGP9J7">pic.twitter.com/g6uYtGP9J7</a></p>&mdash; Andrew Schmelyun (@aschmelyun) <a href="https://twitter.com/aschmelyun/status/1506960015063625733?ref_src=twsrc%5Etfw">March 24, 2022</a></blockquote>

So let's dive in and I'll show you exactly what I used, and how I set it up!

## Hardware list

In order to get started, I'll need a thermal receipt printer and some way to get data into it. I ended up using:

- Epson TM-T88IV
- Raspberry Pi Zero W
- Micro USB to USB adapter
- USB Type-B cable

The reason that I went with an Epson thermal printer is that they use the ESC/POS command set, for which there's [established libraries](https://github.com/search?q=esc%2Fpos) in a variety of programming languages. Plus they're pretty ubiquitous in the second-hand market, and I was able to pick one up on Ebay along with some receipt paper for a pretty fair price.

The other piece I need is some kind of hardware to connect from the internet to the printer, and facilitate the actual data sending. I could just hook it up to my PC, but I want this to be a fully-contained unit that could just be constantly on idle sitting in a corner. I have an old [Raspberry Pi Zero W](https://www.raspberrypi.com/products/raspberry-pi-zero-w/) laying around that I'm not using, so I'll choose that. 

Because the RPi Zero has just a single micro USB port, I'll use an adapter as well as a USB Type-B cable to connect it to the receipt printer.

## Sending data to the printer

Alright, so we have the printer hooked up, the Raspberry Pi good to go, but now I need a way to send data _to_ the printer _from_ the Raspberry Pi. This could easily be accomplished with Node or Python, but since I'm a PHP developer and I enjoy stretching the limitations of the language, I'll reach for that. Luckily for me, there's a [pretty solid library](https://github.com/mike42/escpos-php) for working with ESC/POS commands available in PHP.

Before I write any code though, I have to make sure the printer is available to the program I create. Since I'm using Ubuntu on the Raspberry Pi, I should be able to access it via `/dev/usb/lp0` (or another lp#). But it might require a little bit of prep work first.

First, I'll open up a terminal in the device that my printer is connected to (for me, that's the Raspberry Pi). I'll run the command `lsusb` to get the _Product ID_ and _Vendor ID_ from the connection to your printer. It returns something like this:

```bash
Bus 002 Device 001: ID 04b2:0202 Epson TM-T888IV Device Details
```

Next, I create a udev rule to let users belonging to the **dialout** group use the printer. I create the file `/etc/udev/rules.d/99-escpos.rules` and add the following to it:

```bash
SUBSYSTEM=="usb", ATTRS{idVendor}=="04b2", ATTRS{idProduct}=="0202", MODE="0664", GROUP="dialout"
```

Being sure to replace the hex values for the vendor and product ID's with what I got returned back from `lsusb`.

If my user(s) aren't part of the *dialout* group, I try to add them to it now:

```bash
sudo usermod -a -G dialout pi && sudo usermod -a -G dialout root
```

And then finally, I have to restart udev:

```bash
sudo service udev restart
```

Now that I have the connection ready, I can start writing some code to test this out. First, I'll require that library from earlier with Composer:

```bash
composer require mike42/escpos-php
```

After that's installed, I need to write some code to send data to the printer. I'll create a file called `index.php`, and add the following:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\Printer;

$connector = new FilePrintConnector('/dev/usb/lp0');
$printer = new Printer($connector);

$printer->text('Hello, world!');
$printer->feed(2);
$printer->cut();
```

So now to run this, all I have to do is execute the script with PHP and root permissions:

```bash
sudo php index.php
```

If everything worked out fine, **Hello, world!** will have printed on a receipt, with two lines skipped, and then the receipt will have cut. How that all works is pretty straightforward.

A print connector is created to the 'file' `/dev/usb/lp0`, which is the usb adapter that the printer is connected to. The printer commands that are subsequently used (`text()`, `feed()`, `cut()`), stream the raw commands associated with those actions to the printer through that connection.

> **Note**: If you get an error about permissions when sending to `/dev/usb/lp0` or something similiar to that, try running `sudo chmod +777 /dev/usb/lp0` and seeing if that fixes it.

Using these methods, I can move on to connecting this with GitHub and populating the receipts with some actual data.

## Connecting to GitHub

GitHub makes it easy to listen to events on repos with [webhooks](https://docs.github.com/en/developers/webhooks-and-events/webhooks/about-webhooks). By going to one of my repo's settings page and navigating to the webhooks section, I can create a hook that will POST to a specific URL on a given action. For my case, I want to print out a ticket when a new issue is created, so I choose just the 'Issues' section. I also set the data type as JSON, since that's what I enjoy working with.

But before I continue, I need to have a URL that GitHub could _send that POST request to_. First, I ssh back into the Raspberry Pi and start up the local PHP server by using the -S flag in my project's directory:

```bash
sudo php -S 127.0.0.1:8000
```

Now that it's running, I need a way to access that port on my Raspberry Pi, while it's on my local network. I don't really want to expose my home's IP address or worry about creating a pass through my router. So, I just ended up using [ngrok](https://ngrok.com/) to tunnel through to the exposed port.

```bash
ngrok http 8000
```

After that loads up, I copy the provided https url, and paste it in the GitHub webhook url field. Everything looks good, and I save the webhook. As soon as I save, there should be a test request that's sent out, ngrok accepts the request, tunnels it to the local PHP server, and another **Hello, world!** will print out.

Now I'm ready to actually use the incoming request from GitHub to build out a ticket.

## The final code

Now I'll make some modifications to my code from earlier. First, I should discard anything that's not a POST request. So before initializing the FilePrintConnection, I add these lines:

```php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return 'Error: Expecting POST request';
}
```

And after the FilePrintConnection and Printer initialization, I'll decode the entire JSON request from GitHub as an associative array:

```php
$data = json_decode(file_get_contents('php://input'), true);
```

Now, I can use the printer methods from before and the data array from GitHub to build up the receipt I want! Using the Escpos library, formatting text requires a **lot** of repetitive code. For a small example, here's what a bold and underlined title of the issue, along with the plain text body, looks like:

```php
$printer->setUnderline(true); // start underlined text
$printer->setEmphasis(true); // start bolded text
$printer->text($data['issue']['title']);
$printer->setEmphasis(false); // stop bolded text
$printer->setUnderline(false); // stop underlined text

$printer->text($data['issue']['body']);
```

> If you'd like to see the entire code that I used to format my ticket in the tweet above, you can check it out on the [GitHub repo](https://github.com/aschmelyun/github-receipts).

Now to test it out, all I have to do is go to the repository where I set up my webhook, create a new issue, and wait for the printer to deliver a ticket :magic:.

## Wrapping up and next steps

Alright, so where to go from here? This is definitely a simple proof of concept, but we can **expand on it** a few different ways.

For the tickets themselves, a QR code could be added to link directly to the issue on GitHub. You could also add in more details from the issue itself like tags and severity.

You could also use this concept to handle basically any data coming from a webhook or through an API request. Like printing tickets from apps like Jira or Bugsnag, exceptions thrown from production applications, or even daily todo items and grocery lists!

So, what do you think? If you have any ideas for how you'd improve on this setup, or just have a question or comment, please let me know in the discussion below or on my [Twitter](https://twitter.com/aschmelyun)!

<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>