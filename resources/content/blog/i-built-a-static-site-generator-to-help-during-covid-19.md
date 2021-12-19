---
view: layout.post
title: I built a static site generator to help during COVID-19
cover_image: https://dev-to-uploads.s3.amazonaws.com/i/t9mwl17ssvl6mp8vtcgd.png
description: For showcasing local restaurants still open during coronavirus, I built a framework that uses Markdown files and creates a local directory website
categories: laravel
published: Mar 31 2020
excerpt: A few months ago, I published an article about a static site generator I made called Cleaver. Before this weekend, I mainly was just letting it sit idle. Fixing a few issues that sprung up, figuring out what features I should be adding to it, et cetera.
---

**tl;dr: See [github.com/aschmelyun/cleaver-directory](https://github.com/aschmelyun/cleaver-directory) for more info and a quick-start guide.**

A few months ago, I published [an article](https://dev.to/aschmelyun/cleaver-a-blazing-fast-static-site-generator-using-laravel-s-blade-1k32) about a static site generator I made called Cleaver. Before this weekend, I mainly was just letting it sit idle. Fixing a few issues that sprung up, figuring out what features I should be adding to it, et cetera.

All last week I've been thinking and trying to come up with something that I could build to potentially help, in any way, those around my during the COVID-19 pandemic. I've noticed the large amount of dashboards showing statistics, infection rates, and the like, and I honestly wanted to shy away from that purely for my own sanity. I wanted to do something that would make an impact, and potentially relieve stress, on local businesses in my small Florida city. So, **I came up with an idea**.

## The idea

I decided to build a local directory, a website map of all non-chain restaurants in my county that were still open and serving take-out and curbside food. The goal being to spread awareness to others about dining options that are still available, and hopefully funneling a source of business into local establishments.

The basic gist would be a landing page consisting of a map, along with a list of restaurants that could be filterable by tags, city, or a text box for user input. Clicking on a restaurant would give you details like their open hours, what type of food they're serving, if they have a limited menu, etc.

Also, I wanted this to be **100% open source and easy for other developers to use**. That way, someone else could clone this project, and spin it up on a server, and add data for their local city or area. Originally I was planning on using a traditional Laravel backend for this, combined with a MySQL database and Vue frontend. But, I thought, that might hinder some people using it.

Not everyone can, or wants to, purchase hosting that can accommodate those requirements for a non-profit community project. So I went deeper. What if I used flat-file storage, or better yet, **what if the whole site could be built into static files and served on a $0 Netlify instance**? Then my Cleaver static site generator came to mind, and I started hacking away at it.

## The dev process

As it previously stood, Cleaver was incredibly simple. It took Markdown or JSON files as content, looped through them in an array, and with the help of Laravel's Blade templates, built out HTML files that were saved depending on the path you specified. This was great for something like a blog, or a documentation site, but in order for this to work the way I wanted it was missing a crucial piece.

On the home page, I needed to have a running list of all of the restaurants available throughout the site. The problem was that I didn't want to have to manually add each one as an individual content file, AND add it to an index page as well. My goal was, **add a restaurant Markdown file and the landing page will automatically pull it in**.

It honestly was easy enough to accomplish using Tighten's Collection package. If you're not familiar with Laravel, Collections are basically arrays on steroids. There's [literally dozens of methods](https://laravel.com/docs/5.8/collections#available-methods) that you can use to filter, map, sort, and loop through items you add. In my case, I added a block of code that adds every single piece of content across the site as a collection, and makes it available to each template as a `$content` variable.

That way, to get my list of restaurants on my index page, I was able to do something like this:

```php
$listings = $content->filter(function($item, $key) {
    return $item->view === 'layout.restaurant';
});
```

Since only the restaurant pages used the `layout.restaurant` view, I could filter through the array of content and pull in all the restaurants with their subsequent data. Then, just like an array, I could call a foreach loop on the collection and spit out the details for each of them.

The rest of the development process was pretty easy and straightforward. Using TailwindCSS and a few Blade templates, I hacked together a landing page, a content page (for things like FAQs and contact info), and individual listing pages. The landing page is powered by Vue and contains an embedded Google Map, and live filtering of restaurants. All you have to do is add Markdown files following a specific format and fill in some brief details.

An example restaurant Markdown file might look like this:

```md
---
view: layout.listing
path: /taco-dive
tags: delivery,take-out,mexican,curbside
title: Taco Dive
address: 10501 SW Village Center Dr
city: Port St. Lucie
state: FL
lat: 27.267700
long: -80.432040
---

Not your average hole in the wall. Popular spot for dive tacos, sandwiches, burritos, and salads.

Currently serving a limited menu, for their full menu see [their website](https://tacodive.com).

**Hours:**
- Mon-Sat: 12pm-8pm
- Sun: Closed
```

So, let's say you want to get started and create a directory of your own, here's how it works!

## Building your own directory

Before you get started, there are a few requirements if you want to compile the site and assets locally on your machine. They are:

- PHP 7.1+ installed
- A fairly recent version of Node + NPM

First step, is clone the repo or use Composer to create a new project at a directory of your choosing:

```bash
composer create-project aschmelyun/cleaver-directory my-directory
```

Once that's ready, you'll need to install the Node dependencies:

```bash
npm install
```

Next, it's time to fill your directory with listings! These could be restaurants, shops, or any other businesses that you want to appear on the map. In the `resources/content/listings` directory, copy one of the example Markdown files and make your necessary edits.

It should be noted that the filename you choose to save your content as doesn't matter. The `path` variable that you set in the file will determine where your page ends up living on your directory.

After you're satisfied with your content choices, the last thing to do is build your site! From your command line in the root directory, you have two choices:

```bash
npm run production // this will build your assets quickly, but is recommended for local development
npm run production // this will build, minify, and transpile your assets
```

Your compiled HTML files will be put in a `dist/` directory in your project's folder. It will contain all of the compiled assets, directory structure, and rendered markup for your entire site.

Let's get it up and online!

## Deploying your site

If you're using your own server, it's as easy as either:

- Uploading just the `/dist` directory to your webroot, or
- Uploading the entire project and modifying the webroot of your server to use the `/dist` folder

From there, you should be able to see your finished site!

But what about that **$0 deployment from Netlify** that we talked about earlier? It's pretty simple. Just follow these brief steps:

First, sign up for an account at [Netlify](https://netlify.com) if you don't already have one.

Then, upload your entire finished project to a GitHub repository, with or without the /dist folder attached to it.

Go into your Netlify dashboard and create a new site from a GitHub repository, choosing the project that you just committed.

In the build settings, you're going to type in `npm run production` as the build command, and `dist` as the publish directory. Additionally, in the Environment Variables section, you'll have to specify `PHP_VERSION` as `7.2`, since 5.6 is by default the one used.

![Screenshot of Netlify build settings](https://dev-to-uploads.s3.amazonaws.com/i/dimdy7crgxkor4gzt9ne.png)

That's it! After hitting **Trigger deploy**, Netlify will clone your site, install the Composer and NPM dependencies, run the asset compilation, and output the entire site to the `dist` directory. That directory is then served out to the public and your site is ready to be viewed!

All you have to do is either share the randomly-generated domain given, or add in a custom domain name to attach the project to.

## That's about it

Well, that's all there is! This project is still very much a work in progress. As it was hacked out over a literal weekend, there's a lot of room for optimization, organization, and general additions.

Please feel free to add any issues, bugs, or feature requests that you might come across to the [GitHub repo](https://github.com/aschmelyun/cleaver-directory). You can also reach out to me on [Twitter](https://twitter.com/aschmelyun) if you have any questions about this project, or web development in general. 