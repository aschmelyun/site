---
view: layout.post
title: Fast and cheap ways to deploy a Laravel application
description: From shared hosting to serverless, there's a lot of options to choose from when it comes to deploying a Laravel application.
categories: laravel
published: Jul 3 2023
excerpt: So you have a Laravel application and you want to get it deployed without breaking the bank. Being that Laravel is built on PHP, there's a lot of options to choose from but all of them might not be too good for your wallet. I'll take this time to go through five of my favorite cheap and easy options to deploy a Laravel application.
---

So you have a Laravel application and you want to get it deployed without breaking the bank. Being that Laravel is built on PHP, there's a _lot_ of options to choose from but all of them might not be too good for your wallet. I'll take this time to go through **five** of my favorite cheap and easy options to deploy a Laravel application.

## Shared Hosting

Starting off with the most basic, we have shared hosting. This is pretty well-known, and it's a simple solution that usually costs a few dollars (US) a month. You sign up with your hosting provider of choice and get access to a management dashboard like cPanel.

From there, you can configure your options, upload your source code, and get your application deployed onto the public web. But, you can usually run into an issue with this. Most shared hosting that I've come across has the root folder set to something like `public_html`, but Laravel's root folder is the `public` folder. We can work around this in two ways. 

The first is to drag everything from the Laravel application into the top-level directory and just rename the `public` folder to `public_html`. Simple fix, but it can get messy if you have other applications on the same server.

The second is to create a folder called `laravel` and follow [these steps](https://github.com/hannanstd/change-laravel-public) to point the application's internal reference of the public folder to the `public_html` directory that exists outside of this folder.

After deploying the application, you _might_ run into an error when trying to log in. This is because Laravel's core code still expects the folder to be named `public`. To fix this, open up the source code and add these lines to the `AppServiceProvider.php` class:

```php
$this->app->bind('path.public', function() {
    return base_path('public_html');
});
```

> Note: In my example application, only the second line fixed the issue, but your mileage may vary.

Okay, moving on!

## Virtual Machines

The second option is a cheap virtual machine (VM) like a [DigitalOcean](https://digitalocean.com) droplet. You can start from scratch or use one of their marketplace installs that sets up a boilerplate for you. They have ones specifically created for Laravel applications that come pre-installed with PHP, MySQL, and Nginx.

Once you have your VM up and running, you can use `rsync` or plain old sftp to copy your application files to the server. After that, you should be good to go! If you didn't use a marketplace install like the ones mentioned above, you'll have to make sure that your webserver public root is set to the `public` folder of your Laravel application.

Third option, let's talk serverless!

## Serverless

If you want to go serverless, you can use Amazon Lambda with the [Bref](https://bref.sh/) library. This allows you to deploy your application to the AWS cloud and (depending on your traffic), stick within the free tier provided. The only downside is that you will need to use an external database for persistence.

To get started, you'll need to install the [serverless framework](https://www.serverless.com/) on your host machine and set it up with your Amazon account. Then, you can require the Bref library in your Laravel application using Composer.

```bash
composer require bref/bref bref/laravel-bridge --update-with-dependencies
```

The configuration file can be published for making any desired changes.

```bash
php artisan vendor:publish --tag=serverless-config
```

Once everything is set up, clear the config cache and use the `serverless deploy` command to bundle up the application and send it to AWS. After a few minutes, you should be able to access your application through the URL provided in the output.

Want a more hands-off serverless approach?

## Laravel Vapor

If you want to stick with serverless but don't want to deal with the hassle of setting up Lambda, you can use [Laravel Vapor](https://vapor.laravel.com). This is a first-party service provided by Laravel and offers a modest free tier. It acts as a high-level wrapper for the AWS services mentioned in the last option, and allows you to manage your application through a spiffy dashboard.

You can connect your source code through GitHub for automatic deployments, and manage databases, caches, and domains all in one place. Vapor works similarly to the serverless CLI that we used with Bref. 

In order to get started, you'll need to install the Vapor tool on your host machine. Next, run `vapor login` to connect your account. Then, you can run `vapor deploy` from your project root to deploy your application. After a few minutes, your Laravel app will be live on a domain for you to use and share!

ALright, we're almost done!

## Fly.io

The last option I'll mention is [fly.io](https://fly.io/). This is a containerized hosting service that comes with another nifty command-line tool that makes it easy to deploy Laravel applications.

The pricing is transparent, but it can be a bit challenging to figure out the exact cost when signing up. I've personally found that a Laravel app running on their second smallest CPU costs _about_ three dollars a month.

To get started, you'll need to install the `flyctl` tool on your operating system and then run `fly launch` from your project root to configure the deployment settings. Once you're ready to deploy your project, you can simply run `fly deploy` and within a few minutes, your application will be live on a public domain.

Like with Laravel Vapor, fly.io offers a dashboard where you can monitor the health and metrics of your application, view deployment history, manage various services associated with your account, and more.

That's about it!

## Conclusion

Wrapping up, I do want to say that I haven't been paid to promote any of these services. I've just used them all at some time in the past and have personally found each to be great options for deploying Laravel apps. Most offer free tiers, and all of them are easy to use and powerful enough to handle most small-to-medium applications.

 If you have any questions or need some guidance when it comes to setting up any of the options above, feel free to reach out to me on [Twitter](https://twitter.com/aschmelyun) or by sending me an [email](mailto:me@aschmelyun.com).
