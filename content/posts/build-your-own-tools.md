---
title: Build your own tools (even if you reinvent the wheel)
slug: build-your-own-tools
description: Instead of doom-scrolling through my phone in the morning, I built an alternative with a Raspberry Pi, a dot matrix printer, and some PHP. 
categories: discussions
published_at: May 11 2025
excerpt: For a while now I've started my day by unlocking my phone and scrolling through different news and social media sites to see what's going on. It's not exactly great for my mental health and I've been trying to cut down on screen time for a while. I still want to stay up-to-date though, especially after I get up in the morning. What about a custom "front page" printed out and ready for me, instead.
---

I'm a huge believer that if you're a software engineer, **you should be building your own tools**.

Before we start talking about caveats and time management, let me just elaborate on this a bit through an analogy.

Woodworkers famously use a variety of [jigs](https://www.youtube.com/watch?v=FkBMfFyHstQ) for their projects. These are often pieced together with leftover scraps around the shop and are used for very specific cuts and assemblies. For instance, cutting the same exact angle on multiple boards, or ensuring that a dovetail always lines up on the first try.

Some of these jigs exist as purchasable hardware, often expensive or over-complicated, so plenty of woodworkers prefer to go their own route and build a jig that fits their exact needs.

This task is often a rite of beginner woodworkers, as making one can be a lesson in itself and provide a deeper understanding of the math or geometry involved while working on a particular project.

**I think the same rings true in the programming sphere.**

To start, let's talk about [dotfiles](https://www.reddit.com/r/unixporn/comments/1afxmt5/what_are_dotfiles_and_how_do_you_use_them/). Plenty of developers have theirs in a public repo, for backup purposes, if others want to copy specific settings, or to allow for a peek at what their local development environment looks like. Often these include small bash or shell scripts to help with automation of tedius or repetative tasks.

One of my personal favorites has been a `mkcd` command I've kept in my shell config for the last decade:

```sh
mkcd() {
  mkdir "$1"
  cd "$1"
}
```

This is a trivial example, but I've seen some developers go absolutely crazy with these configurations. Dozens of functions, mutiple hand-crafted plugins, all representing some internal struggle that developer solved for themselves. Not by reaching for an existing tool with a complicated setup process, or abundance of unnecessary features, just a few lines of code to meet their _specific_ needs.

Shifting gears into a bit more complicated realm, let's talk about blogs and websites.

The CMS is, for the most part, a solved problem. There exists [dozens](https://statamic.com) [of](https://wordpress.com) [options](https://craftcms.com) [for](https://strapi.io) [CMS](https://contentful.com) [platforms](https://ghost.org), both paid and open source, that can manage blog posts, pages, projects, contact forms, and much more.

But, I chose to ignore all of those options when I built this website. It's a fairly [simple app](https://github.com/aschmelyun/site) built on Laravel with a library to render Markdown files into HTML (like the post you're reading right now).

Why? My biggest wants were:

- Lightweight with not a whole lot of features
- Write posts in Markdown
- Be able to create interactive segments using Laravel

So instead of hacking something together with an existing solution and _also_ having to use a microservice API, I just built it myself to solve my particular use case.

The best part is that **I learned some things doing it!**

A lot of us, myself included, don't get the chance to experiment with new frameworks, languages, or libraries in our daily jobs. Knocking out a little app or tool that helps you with some weirdly frustrating or incredibly specific problem is a great way to inject that hands-on learning and level up your existing skillset. (Of course, assuming you have the energy and time to do so.)

If you share your work online, don't be discouraged by comments about "not reinventing the wheel", or "this doesn't help anyone else". Creating something with software for the sake of solving your own problems is a rewarding feeling, and if that's where the usefulness stops, that's just fine.

Also, sometimes the wheel also gets better by reinventing it. You don't see my car driving on stone tires!
