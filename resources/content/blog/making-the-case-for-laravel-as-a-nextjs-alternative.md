---
view: layout.post
title: Making the case for Laravel as a Next.js alternative
description: Next.js offers a lot of features out of the box, but what if you could get all of that with just PHP? Let's take a look at what Laravel can provide.
categories: laravel
published: Aug 18 2023
excerpt: Next.js is a powerhouse of a full-stack web framework with features like page-based routing, static-site generation, and dynamic React components, but what if I told you that you could get all of that with just PHP? Let's take a look at what Laravel can provide.
---

[Next.js](https://nextjs.org) is a powerhouse of a full-stack web framework with features like page-based routing, static-site generation, and dynamic React components, but what if I told you that you could get all of that with **just PHP**?

[Laravel](https://laravel.com) is a PHP framework that's been in development for almost a decade. In that time it has solidified itself as one of the most-loved options for building web applications powering the needs of both startups and some of the largest companies around. It also has amassed a massive community of individuals and organizations that provide world-class packages and libraries to enhance its already renown developer experience.

Within the last year or two, first-party upgrades to the framework and package developers have added a suite of functionality to make Laravel a powerhouse for developing truely full-stack web applications fast and efficiently. In this article I'm going to go through some of the biggest features that Next.js is known for, and offer up examples of similarities found in the Laravel framework. 

This isn't meant to be a "who's better" between the two, but instead a showcase of what Laravel can do out of the box. **Let's start!**

## Page-based routing

Next.js is famous for its simple and intuitive page-based routing system. But did you know that Laravel has a similar functionality now? Up until this year, if you wanted to create a new route in Laravel you'd have to specify it in a main routes file. However, a first-party package called [Laravel Folio](https://github.com/laravel/folio) was recently released that enables purely **page-based routing**, and it works surprisingly similarly to Next.js.

Let's take a look at an example file structure:

```
resources/
└─ views/
   ├─ pages/
   │  ├─ index.blade.php
   │  ├─ uses.blade.php
   │  └─ blog/
   │     ├─ index.blade.php
   │     └─ [slug].blade.php
   ├─ dashboard.blade.php
   └─ welcome.blade.php
```

In the above, anything under the `resources/views/pages` follows the convention of page-based routing. So the `uses.blade.php` file's content will be visible at our site under `/uses` .

An index.blade.php file is used to denote the default root view for that particular directory. That means that the `blog/index.blade.php` content will be visible at `/blog`. This should seem straightforward and similar to Next.js.

Except, what about dynamic routes? Well, that's covered too!

In the above example, you'll see a `[slug].blade.php` file. This creates a dynamic route where anything visited under the blog route will be resolved by this view file, with a `$slug` variable being included that contains the data passed through to the route.

For example, visiting `/blog/my-example-post` will resolve to that **[slug].blade.php** page, and if you referenced a `$slug` variable in it, will contain the value "my-example-post". This can then be used with an internal function to grab a related model, make an API request, or however else you want to use the value passed through.

But we can take dynamic routes *one step further*.

For our blog, if we were using a Laravel model called `Article` to hold data for a single blog article, we can actually use route-model binding in our dynamic page-based routes by just renaming that file to `[Article].blade.php`. Now inside the blade file, the variable `$article` will be available, containing the entire Article model's attributes!

## Static-site generation

Next.js recommends using static generation whenever possible, and it's enabled by default on most pages. Basically anything that doesn't require heavy user interaction can be statically generated on build time and then served up via your server (or better yet, a CDN).

By default Laravel generates every route dynamically. It's not slow since there *is* a route cache which helps cut down on a lot of the PHP interpretation, but the fact still remains that a Laravel app relies on dynamic markup generation.

We can change that though!

There's a package, [laravel-export](https://github.com/spatie/laravel-export), that comes from a well-respected third party. After installation there's **zero necessary configuration** before our entire site can be statically generated with a single command, `php artisan export`. 

This will crawl our site, following links on pages, and save the rendered markup to a `dist` folder in our application's folder structure. It also works well with the Folio page-based routing mentioned above. 

Like with Next.js, we can modify the behavior of this through some small configuration changes in our app.

We can specify paths to exclude from the crawler, programmatically turn it on or off, and write hooks that fire off commands before or after the export runs (for example, if we wanted to deploy the exported site files to a production server). 

Unlike Next.js, there's no equivalent of a `getStaticProps` method that runs when building the exporter. You can always include a function in the body of the Blade template if you're using something like Folio, which will run as each page is compiled, although that'll also occur *outside* of the exporting as well.

## Server-side rendering

While using Next.js you need to include a `getServerSideProps` method to enable SSR, but PHP comes with server side rendering on by default!

In normal circumstances, PHP is rendered on each request through the server. This means that writing PHP code in your blade files guarantees that the content of your functions will be hidden from the generated markup that comes back to the browser. Those files are never accessed *directly*, and so this adds a layer of security to your application by default.

Going deeper than just blade files, Laravel is a full MVC framework and so includes things like Models and Controllers out of the box that can be used to organize your server-rendered code. Authentication is also baked in by default, and with first-party packages like [Breeze](https://github.com/laravel/breeze), [Sanctum](https://github.com/laravel/sanctum), or [Socialite](https://github.com/laravel/socialite), you can include user registration, login, API-based authentication, social sign-ups, and role-based permissions with near zero configuration.

A similar out of the box piece to look for is a database. Laravel includes an abstraction layer called [Eloquent](https://laravel.com/docs/eloquent) that makes it easy to interact with your database of choice. It's a full ORM that can be used to create, read, update, and delete records, and can be used to build out more complex relationships between models.

## Single page components

Some of the beauty of Next.js comes from the fact that you're using the React library, and all of the wonderful developer experience that comes with it. This enables you to easily build dynamic user interfaces and responsive views with relative ease.

Everything can also be self-contained inside a single page component. Can this be done inside of our Laravel app since everything is server rendered by default?

Up until recently we've had to install and configure a frontend framework to get that functionality, but that requires a separation of concerns and the maintanence of two different codebases. Instead, we can use [LiveWire](https://laravel-livewire.com) and Volt to give us dynamic, single-page components with *just* PHP.

Let's say that in our `[Article].blade.php` page mentioned way above that after each page's content we have a subscription form. We can use these two packages to build a dynamic component into our existing page using PHP and Laravel. It might look like this.

```
<?php
 
use App\Livewire\Forms\SubscribeForm;
use function Livewire\Volt\{form};
 
form(SubscribeForm::class);
 
$save = fn () => $this->form->store();
 
?>
 
<form wire:submit="save">
    <input type="email" wire:model="form.email">
    @error('form.title') <span class="error">{{ $message }}</span> @enderror
 
    <button type="submit">Subscribe</button>
</form>
```

Now when our page is visited or rendered, we'll have a form at the bottom that the user can fill out and submit without needing to perform a full page refresh!

Unlike Next.js though, this interactivity is **dependant on server rendering**. LiveWire uses JavaScript on the frontend to hydrate these components and provide client-side interactivity, but the core functionality and reactivity depends on the server to manipulate state and perform the functions requested.

## Development environment included

With Next.js, you have an included development server that's ran with npm. With Laravel, there's a few different options for a local development environment.

First, if you have PHP installed locally on your system, then it's as easy as running:

```
php artisan serve
```

This boots up a local PHP server instance, included in the actual *language* itself. It's a bit limited, blocking by nature and without a database, but it gets the job done and is responsive enough to use for 90% of local development cases.

If you want something more complex with additional features like a MySQL server, Redis instance, or Mailpit box, then you can use the first-party Laravel Sail which comes pre-installed to new Laravel apps. It's a bash script wrapper for Docker that boots up a network of containers and handles your local dev environment setup.

## Ease of deployment

Alright, you're ready to release your application to a production environment, now what? Let's assume that we have a mixture of static content and server-side rendered routes, which means we'll need to have a setup that can run our applications dynamically.

For Next.js, this means that we need to have a server provisioned with Node.js. Something like a basic Amazon EC2 instance or a DigitalOcean Droplet can handle that, and the installation and configuration is pretty straightforward. What about Laravel?

Since Laravel runs on PHP, and PHP has been around for decades, getting a server provisioned with the requirements for a LAMP (or LEMP) stack is not too difficult of a task. There's a plethora of options available, from shared hosting to VPS providers. Even managed services like [Laravel Forge](https://forge.laravel.com) that can handle the provisioning and configuration of your server for you, similarly to how Next.js has managed application instances with Vercel.

## What else?

This article wasn't meant to be a competition between Next.js and Laravel. They're both fantastic batteries-included, full-stack web frameworks that enable you to build applications and ship awesome features.

However, if you're looking for an alternative to Next.js, maybe with something that's a bit more batteries-included, Laravel could be what you're looking for. With a robust ecosystem, a passionate community behind it, and the ubiquity of a language powering over 70% of the web, it's definitely an option to keep in mind.