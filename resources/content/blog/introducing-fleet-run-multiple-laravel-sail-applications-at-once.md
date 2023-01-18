---
view: layout.post
title: Introducing Fleet, run multiple Laravel Sail applications at once
description: Fleet adds a few helpful Artisan commands to manage Laravel Sail applications and binds them to custom domain names.
categories: laravel
published: Jan 17 2023
excerpt: This package exposes a few helpful Artisan commands to manage multiple Laravel Sail applications running concurrently on your local environment. All powered by a Traefik Docker container, letting you map custom domain names to your different Laravel applications and handling the incoming traffic to them.
---

This package exposes a few helpful Artisan commands to manage multiple Laravel Sail applications running concurrently on your local environment. This is all powered by a [Traefik](https://doc.traefik.io/traefik/getting-started/quick-start/) Docker container acting as a reverse proxy, letting you map custom domain names to your different Laravel applications and handling the incoming traffic to them.

**Want to skip this and get started now?** Check out the [GitHub repo](https://github.com/aschmelyun/fleet) for some stream-lined instructions.

## Backstory

[Laravel Sail](https://laravel.com/docs/sail) has pretty much become the standard for starting up development environments for new Laravel projects. Under the hood it's using Docker and Docker Compose to manage services and dependencies for your project, booting up containers to handle local traffic and serve your application.

I enjoy using it, but there's a slight problem I ran into the other day with it, and that's running multiple applications locally at the same time. When Sail boots up, a port is bound from your local machine into the Docker container that the application is running on (by default, it's :80). If you go and try to boot up another Laravel application with Sail, it'll kick back an error because that port is already taken.

Now, you can go ahead and change that port by adding in a `APP_PORT` entry to your project's env file, but that comes with a caveat. Depending on what you're doing (in my case, two applications communicating with one another), modern browsers handle localhost with two different ports as _completely separate domains_. Instead, what I wanted was something like `app.localhost` and `dashboard.app.localhost`. So, I decided to build [Fleet](https://github.com/aschmelyun/fleet)!

## Getting started

After installing a new Laravel project (or using an existing one with Sail installed), open up a terminal window and navigate to your project's root. Then, install Fleet with Composer:

```bash
composer require aschmelyun/fleet
```

Next, you'll want to make sure that any running Sail instances have been stopped. Now we can go ahead and add Fleet support to our first application. Again in the project root, use the following command:

```bash
php artisan fleet:add
```

During setup you'll be prompted to enter in a domain name of your choosing. Alternatively, you can pass it in through a command-line argument:

```bash
php artisan fleet:add my-app.localhost
```

> Note: If you chose a domain that doesn't end in .localhost, you will need to add an entry to your hosts file to direct traffic to 127.0.0.1!

After the setup finishes, you can start up Sail just like normal:

```bash
./vendor/bin/sail up
```

After the initialization finishes up, your app should be now be available at the domain you provided!

## Additional commands

By default, whenever you use `fleet:add`, a Docker network and container are both started to handle the traffic from your local domain name(s). There are a few additional Artisan commands that you can use to manage Traefik or remove Fleet support from your application.

- `php artisan fleet:start` boots up the Traefik container and network that normally start when using fleet:add
- `php artisan fleet:remove` removes Fleet support from an application and returns it back to the default Sail setup
- `php artisan fleet:stop` stops all Fleet containers and networks currently running on your system

## How's it work?

When the `fleet:add` command is ran Fleet parses your application's `docker-compose.yml` file and makes some adjustments to it. The port binding for the app service container is removed, and labels are added in to support mapping for the custom domain name you provided during setup. Also a mapping to an external Docker network is added to the array of networks.

After that finishes up successfully, it runs the `fleet:start` command. This spins up both a Docker network and container called `fleet`. The container is running Traefik, a reverse-proxy application written in Go. This attaches to the network of the same name. You're then told that you can spin up Sail just like you would normally.

When using `sail up` again, this time your application isn't exposed via a port binding. Instead, Traefik determines that the container is spinning up and using the labels that were added to the service in the `docker-compose.yml` file, sets up a traffic map from your local environment to the container based on the custom domain you set.

So if you set your domain to `my-app.localhost` for one of your Laravel applications running in Sail, visiting that domain will display _that_ Laravel app. But going to a different `.localhost` domain will result in a 404 (until you set it up 😉).

## Wrapping up

So, that's Fleet! If you end up using it and have any questions or comments, please feel free to let me know by messaging me on [Twitter](https://twitter.com/aschmelyun) or opening up an [issue](https://github.com/aschmelyun/fleet/issues/new) in the GitHub repo. If you want to know more about Traefik and how to use it to manage multiple local development sites, I published a [video](https://www.youtube.com/watch?v=mZbLvGQqEvY) on that not too long ago.