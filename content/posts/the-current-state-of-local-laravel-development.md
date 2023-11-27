---
title: The current state of local Laravel development
slug: the-current-state-of-local-laravel-development
description: A breakdown of the multitude of options when it comes to creating a local Laravel development environment
categories: laravel,php
published_at: Feb 20 2021
excerpt: Right now there's a lot of options when it comes to working with Laravel on a local development environment. Where there used to be only a handful of options, there's now over a half dozen officially supported ones. In this article, I'm going to try and give a brief synapses of each of them. Provide some pros and cons, along with a basic overview of what you need to get started with each.
---

Right now there's a lot of options when it comes to working with Laravel on a local development environment. Where there used to be only a handful of options, there's now over a *half dozen* officially supported ones.

In this article, I'm going to try and give a brief synopsis of each of them, provide some pros and cons, and include a basic high-level overview of what you'll need to get started with each.

**Want to skip ahead to a particular area?** Here's a list of the methods we'll be talking about:

- [Install a server stack locally](#localServer)
- [Vagrant and Homestead](#vagrantHomestead)
- [artisan serve](#artisanServe)
- [Sail](#sail)
- [Valet](#valet)
- [Laradock](#laradock)
- [Roll your own Docker](#customDocker)

Alright, let's dive right into it!

## Install a server stack locally <a name="localServer"></a>

The old tried and true, installing a full LAMP stack directly on your local development hardware. On a Linux (and to some extent, MacOS) machine, it's not *that* difficult. For Windows users who aren't using WSL however, it can be a little complex if you're not using a pre-built software like WAMP.

**What I like about this method:**
- It's the fastest response time since you're running directly on the machine
- The full stack is ready to go as soon as your computer boots up
- You have the full resources from your machine available to the software stack

**What I don't like:**
- Upgrading your OS can break things like Apache or MySQL
- It can be difficult to get multiple sites running at the same time
- Hardware differences between local and production environments can potentially cause deployment issues

Despite the negatives, this is still a solid way of creating a local Laravel development environment. Getting started can take a bit of time, but it's a pretty straightforward process. You'll be installing Apache, MySQL, and PHP, setting up vhosts rules pointing to a local domain name, and finally enabling the PHP extensions that your application requires.

The best tutorials I've found for this method are as follows for each OS:

- **MacOS**: [https://getgrav.org/blog/macos-bigsur-apache-multiple-php-versions](https://getgrav.org/blog/macos-bigsur-apache-multiple-php-versions)
- **Windows**: [https://gist.github.com/sutlxwhx/cb1c124d560c5a2d21fe94ca25aed1e1](https://gist.github.com/sutlxwhx/cb1c124d560c5a2d21fe94ca25aed1e1)
- **Linux**: [https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04)

After running through the installation and setup process, all you'll have to do is ensure your Laravel app is under the correct path that you set in your web server config, and you should be good to go!

## Vagrant and Homestead <a name="vagrantHomestead"></a>

Homestead is an **official Vagrant box** released by Laravel to help you get a local, containerized environment set up fast. If you're unfamiliar, [Vagrant](https://vagrantup.com) is an application that helps you provision and manage virtual machines, capable of replicating the full stack of software required by your application.

**What I like about this method:**
- Creates isolation between your local hardware and software, and what's required by your application
- Easy to get started with a handful of commands
- Can provision a portion of your system resources, ensuring that your application doesn't use anything in excess

**What I don't like:**
- Even small virtual machines take up a proportionately large amount of system resources
- Response time can be delayed since the filesystem is separated by a virtualization layer
- Vagrant has largely fallen out of favor in most applications for Docker and other modern containerization software

Although some may consider that Vagrant is outdated, being replaced by more modern containerization software, it's still a battle-tested enterprise solution that's been powering and deploying production applications for over a decade.

To get started, all you'll have to do is:

- [Install Vagrant](https://www.vagrantup.com/docs/installation) for your OS
- [Install VirtualBox](https://www.virtualbox.org/wiki/Downloads) for your OS as a provider to Vagrant
- Run `git clone https://github.com/laravel/homestead.git ~/Homestead`
- Create the **Homestead.yaml** config file with `bash init.sh`
- [Configure and launch](https://laravel.com/docs/8.x/homestead#configuring-homestead) the Vagrant box with Homestead

After that, your Laravel application should be available to your browser at `localhost`. There's also a variety of optional services that are brought up with the virtual machine and exposed to your local system (like **MongoDB, Mailhog, and Minio**).

## artisan serve  <a name="artisanServe"></a>

This method follows a similar path as the local server stack, as far as requirements goes, except that essentially you can skip over installing a web server.

You will need to have both **PHP and MySQL** (or your database of choice) installed on your local machine. Behind the scenes it's using PHP's built-in [web server](https://www.php.net/manual/en/features.commandline.webserver.php) to power the command and expose the default port of `:8000` to your local machine.

**What I like about this method:**
- It couldn't be much simpler to get the development server started
- No installation or configuration of a local web server (like Apache)
- Very resource-light

**What I don't like:**
- Response time for large requests can be pretty long
- The web server runs one single-threaded process, so applications will stall if a request is blocked.

In my opinion, if you're just running a single local instance of a development application, this method should work well for you. Combined with something like [Ngrok](https://ngrok.com/) or Beyond Code's [Expose](https://beyondco.de/docs/expose/introduction) for domain masking of your development port, and you're good to go.

To use this method, all you have to do is open a terminal and `cd` to the project directory of your Laravel app. Once there, run:

```bash
php artisan serve
```

Optionally, you can specify the port like this:

```bash
php artisan serve --port=8808
```

You should be notified that a local Laravel development server has been started at `127.0.0.1:8000` (unless you specified a different port). Open it up in your browser, and you're off!

## Sail  <a name="sail"></a>

The newest addition to the *official* local Laravel development family, Sail is a command-line interface that helps you set up, configure, and interact with a default Docker dev environment.

You have the choice of either installing Sail alongside an existing application, or using it to bring up a brand new Laravel app. Sail allows you to run PHP, Composer, npm, and artisan commands **without having to have anything installed on your local machine** except Docker.

**What I like about this method:**
- Zero dependencies need to be installed besides Docker
- Multiple applications can be running at the same time
- Easily build files for production with the `sail:publish` artisan command

**What I don't like:**
- Response time for local Docker instances can be notoriously slow, especially on MacOS (although it's being worked on)
- Because of its aim toward simplicity, Sail can be a little difficult for newcomers to customize out of the box

That being said, it's a fantastic place to start if you've always wanted to try out Docker, or just don't want to have any dependencies installed on your local system. Using this, you can get a full stack web server up and running in minutes.

If you'd like to try it out for yourself, it's pretty straightforward. For existing applications, just run:

```bash
composer require laravel/sail --dev
php artisan sail:install
./vendor/bin/sail up
```

And if you'd like to use Sail to create a new application from scratch:

```bash
curl -s https://laravel.build/my-app | bash
cd my-app && ./vendor/bin/sail up
```

Both of those options will spin up a network of Docker containers using [Docker Compose](https://docs.docker.com/compose/), and expose your application to `localhost:80`.

## Valet  <a name="valet"></a>

**Note: This option is just for MacOS users.**

[Laravel Valet](https://laravel.com/docs/8.x/valet) is a slick, fast, and resource-light development environment that combines Nginx and DnsMasq to proxy all requests to `.test` domain names, pointing them to sites available at a predetermined path on your machine.

There's still a few dependencies required, but not as much as a full web server stack. You'll need **Brew, PHP, and Composer** to get started, as well as some kind of local database (e.g. MySQL or PostgreSQL). Laravel's official docs recommends using [DBngin](https://dbngin.com/) to get that set up.

**What I like about this method:**
- It's blazing fast, as everything is running on your local machine like the first method
- Very little system resources are used for the web server, averaging around 7MB of RAM
- Comes with a wealth of commands to manage your local sites and even switch versions of PHP

**What I don't like:**
- Extensive PHP requests can still take up system resources and bog down your machine
- Does not come with any database management out of the box
- Automatically proxies all `.test` domains, so you're unable to use that domain on an application not using Valet

Valet might be slowly getting replaced by newer containerization methods like Homestead and Sail, but it's still a *powerful* tool to bring up and manage Laravel apps in development.

## Laradock <a name="laradock"></a>

Branded as a "full PHP development environment for Docker", [Laradock](https://laradock.io) is a powerful and feature-rich set of configuration files for local Laravel development with Docker.

This package has been around for a few years, and as such has become one of the de-facto standards used to create both local dev environments and assist in production deployments of Laravel apps.

**What I like about this method:**
- Contains pretty much *any* service you could possibly use in a Laravel application, out of the box
- Configured for both local and production environments, allowing you to easily deploy your application to a server running Docker
- Includes step-by-step documentation for setting up xDebug

**What I don't like:**
- Like with Sail, response time can vary and tends to be on the slower side when compared with other methods in this article
- Tends to be a little bloated, as it's grown over time and expanded beyond just Laravel
- Can be difficult to customize, especially for Docker beginners

The [documentation for Laradock](https://laradock.io/getting-started/) is... extensive, to say the least. However it pretty much boils down to:

- Install Docker for your OS
- Use git to clone the Laradock repo
- Copy the `env-example` file to `.env` in your project root
- Modify the new `.env` file with services you want

After you've completed those steps, it should be as simple as running this command from your project root:

```bash
docker-compose up -d nginx mysql
```

As described in their docs, when bringing up a container like `nginx`, it's dependent on the **php-fpm** service. There's no need to specify php-fpm in the up command, as it'll be brought up alongside automatically.

From there, you'll just have to dig through the documentation and learn to use whatever services your Laravel application depends on. There's separate areas for [caching with redis](https://laradock.io/documentation/#use-redis), [setting up Traefik](https://laradock.io/documentation/#use-traefik), or [running artisan commands](https://laradock.io/documentation/#run-artisan-commands). It might take some time to get fully set up to your liking, but once you do it's a powerful environment with zero local dependencies necessary.

## Roll your own Docker setup  <a name="customDocker"></a>

Our last, and my personal favorite, method for getting a local Laravel development set up, making your own with Docker!

All of the tools above anticipate a generic application's needs and try to wrap it up in a simplistic package for ease of use and access. They're fantastic options, and definite time-savers, *if* your aim is to get a local environment set up as quickly and painlessly as possible.

However, as a full-stack developer who runs side projects from development through to deployment, I really wanted an excuse to dive into the deeper parts of Docker and create a local environment that would fit my needs perfectly.

That's why I made [docker-compose-laravel](https://github.com/aschmelyun/docker-compose-laravel), and it's what I use as the basis for development environments in all of my Laravel projects.

**What I like about this method:**
- Gives you the power to know exactly what services are being used in your local Docker environment
- Full control and customization of your Dockerfiles and docker-compose.yml files
- Can easily be set up for both local development and production environments

**What I don't like:**
- Requires time and effort to learn a new technology before being able to use it
- Debugging issues with Docker containers can often be frustrating
- If using volumes on a non-Linux filesystem, response time for PHP can be fairly slow

Using a combination of resources such as the [Docker documentation](https://docs.docker.com/get-started/overview/) and guides like [this one](https://www.digitalocean.com/community/tutorials/how-to-set-up-laravel-nginx-and-mysql-with-docker-compose) from DigitalOcean, I was able to piece together an environment that both made sense to me and also matched the services that I needed in my applications.

**That's all for now!**

These are the seven most talked about methods of getting a local Laravel development environment set up. There's a lot of options out there, and hopefully this guide might help you narrow down one that works best for you!

If you have any questions about this, or any other web development topics at all, please feel free to reach out to me on [Twitter](https://twitter.com/aschmelyun) or let me know in the comments below.

