---
view: layout.post
title: Launching PDFBlade - Yet Another HTML to PDF API
cover_image: https://dev-to-uploads.s3.amazonaws.com/i/oj8nkzpqbjwiv2w15d8l.png
description: Convert URLs and plain HTML into PDF files using a robust API, easily save them to Amazon S3, and pay only for what you use.
categories: launch
published: Feb 10 2020
excerpt: There's already a few HTML to PDF APIs that are on the market today. They do their job well, and most have pretty detailed documentation that makes it easy to get started. However, my biggest issue was the billing, and I needed to scratch my own itch.
---

**tl;dr: Check out [pdfblade.com](https://pdfblade.com/?ref=dev) for more info**

There's already a few HTML to PDF APIs that are on the market today. They do their job well, and most have pretty detailed documentation that makes it easy to get started. However, my biggest issue was the billing, and I needed to scratch my own itch.

Out of the services that I tried, they all bill per month. Multiple tiers with increasing prices, available options, and most importantly, **quotas**. Some months I'd need to use a thousand or more API calls, other months I might need ten. So, I'm left with two different options in those cases: take the biggest tier I'd need throughout the year and eat the cost in my slow months, or manually switch tiers throughout the year.

I honestly wasn't a fan of either of those options, and so decided to build a service to cater to my (extremely specific) need. Meet **[PDFBlade](https://pdfblade.com/?ref=dev)**. A feature-rich, developer-friendly, HTML to PDF API that has no tiers, and only bills for what you use. Every account comes with a gorgeous, responsive dashboard to manage your settings and keep track of all conversions made with your API key.

## Making Requests

Using the API is super easy, and can be access through either a **GET** or **POST** request. Let's see some quick examples.

Here's one using a GET request with cURL:

```bash
curl -v https://pdfblade.com/api/convert/https://google.com&key=my-key-here&store=true
```

And another using a POST request with the axios JavaScript library:

```javascript
import axios from 'axios';

axios.post('https://pdfblade.com/api/convert', {
    key: 'my-key-here',
    store: true,
    url: 'https://google.com',
    images: false,
    margins: '1,3'
})
.then((response) => {
    alert(response.data);
})
.catch((error) => {
    console.error(error);
});
```

Additionally, there's over a dozen different parameters that you can add to your requests and manipulate the site being rendered, or the final PDF. For example, `pages=2-4` will only return back the second through fourth pages of the returned file, and `images=false` will remove any images from the site before rendering the PDF.

To view more code examples and see a list of all the available parameters, check out the [PDFBlade documentation](https://pdfblade.com/docs?ref=dev) in full.

## Using Responses

By default, the full binary of the PDF is returned when you make a call to the API. You can take this stream of data and save it as a PDF file on your server, or return it back to the browser with an `application/pdf` content type to view it immediately.

Alternatively, we provide a way to both store your files, and limit how many times you'd have to call the API. By using the `store` parameter and setting it to true, **we save your PDF to an Amazon S3 bucket for 48 hours**, and return back the full URL in the response body.

These stored files can be access as much as you'd like within those 48 hours, and if you happen to lose the URL returned it can be found again in your user dashboard.

## Billing

Okay, down to the important part. If this isn't a service that's billed monthly, how *do* I pay for it? Simply put, through credits.

When you make a call to the API, if it runs successfully (and only if it runs successfully) a single conversion credit is deducted from your account. Signing up gets you 100 of them for free, and purchasing new ones can cost anywhere from $2, to $250.

That of course depends on how much you want, and the more you purchase at a time the cheaper each one is individually. Also, they **never expire**. This way you can ensure that you'll only ever be charged for what you're actually using.

Worried about running out of credits? You'll get notified via email when your balance starts running low (at an amount that you can set in your user dashboard), and will get another notification if your balance gets down to zero.

## Get Started

Ready to try it out? Sign up at [pdfblade.com](https://pdfblade.com/?ref=dev) and get your **100 free credits** now!

Going with the theme of my [previous article](https://dev.to/aschmelyun/the-satisfaction-in-treating-your-side-projects-like-bonsai-52cp), I'll be maintaining and iterating on this project for a while. Besides the fact that I need it for my own personal use cases, I want to see this grow and evolve into a service that's useful to novice and expert developers alike.

If you have any questions, please feel free to let me know here, or on my [Twitter](https://twitter.com/aschmelyun). Thanks for your support!