---
title: Build a solid WordPress dev environment with Docker
slug: build-a-solid-wordpress-dev-environment-with-docker
description: Docker and WordPress work together wonderfully to make setting up customizable local dev environments easy and simple
categories: wordpress,docker,php
published_at: Mar 22 2021
excerpt: Before we get started, I'd like to let you know that this article isn’t a deep-dive tutorial into Docker or an explanation on the intricacies of the toolset. It’s more of a casual walk-through that explores the basics in getting a local development environment set up fast using Docker and Docker Compose.
---

## tl;dr

Want to take the fast track and skip the detailed tutorial below? Install [Docker](https://docs.docker.com/docker-for-mac/install/) for your OS, clone [this repo](https://github.com/aschmelyun/docker-compose-wordpress), add your WordPress site files to the **wordpress** directory, and from the root project directory you just cloned run: 

```bash
docker-compose up -d --build
```

Prefer a video walk-through? Follow along with [this tutorial on YouTube](https://www.youtube.com/watch?v=kIqWxjDj4IU) instead.

## A Brief Intro

Before we get started, I'd like to let you know that this article isn’t a deep-dive tutorial into Docker or an explanation on the intricacies of the toolset. It’s more of a **casual walk-through** that explores the basics in getting a local development environment set up fast using Docker and Docker Compose. This comes as an alternative to the traditional method of installing a LAMP stack directly on your machine.

There may be better or more succinct ways of accomplishing this, but the method below is what I’ve found works the best for me when developing WordPress websites.

For those who don’t know what Docker is, let’s enjoy a brief overview. According to opensource.com:

> [Docker](https://github.com/docker/docker) is a tool designed to make it easier to create, deploy, and run applications by using containers. Containers allow a developer to package up an application with all of the parts it needs, such as libraries and other dependencies, and ship it all out as one package.

You can think of Docker like a VM but stream-lined, cutting out a lot of the bloat that virtual machines tend to be known for.

**Why is this helpful or useful?** Well if you have multiple production servers running different versions of WordPress, each one requiring a specific PHP or MySQL version, those variables can be replicated in your container definitions. Then, you can be guaranteed that the application will run precisely how it’s intended to, no matter what base machine Docker is installed on.

Sound exciting? **Let’s dive in!**

## Installing Docker

First, let's grab the installer: [https://docs.docker.com/docker-for-mac/install/](https://docs.docker.com/docker-for-mac/install/).

Run through the typical application installation process, opening it up after it's complete. You may be asked to authorize Docker via your system password the first time you open it, after which you’ll see the little whale icon appearing in your status bar.

The following diagram shows the structure I’ve been using for my local WordPress Docker projects. **You don’t have to follow this exactly**, although the rest of this article will assume your project is set up the same.

```
my-wordpress-sites.com/
├── nginx/
│   └── default.conf
├── wordpress/
│   └── (WordPress source files)
├── docker-compose.yml
├── nginx.dockerfile
└── php.dockerfile
```

In the next few parts I’ll go over what each of these files does, but for now just create them as blank placeholders using the layout above. Additionally, add in (or download) your WordPress site's files under the **wordpress/** directory.

## Creating Our Stack

A great rule of thumb when using Docker is that each container should provide a single service, or handle a single process. Since we’re creating a LEMP stack, that means we’ll need one for our web server (**Nginx**), **PHP**, and **MySQL**. Docker has a fantastic built-in tool to create and orchestrate these containers, called **[Compose](https://docs.docker.com/compose/)**.

All we need to do is define the services (containers) that we need, and at runtime Docker provisions each one and wraps them all in a virtual umbrella network. This means that each service will be accessible from every other container through the use of a hostname.

To get started, create or open up the **docker-compose.yml** file in your project root and add the following to the top of it:

```yaml
version: '3.9'

services:
```

A quick explanation for what we just added:

- **Version: 3.9**, the newest version of the Docker Compose engine, not super useful but opens us up to newer syntax goodies
- **Services:** Where we’ll specify the images that’ll make up our stack

## Adding Nginx

Directly under the services heading that we specified at the bottom of the **docker-compose.yml** file above, you’ll add in the following:

```yaml
nginx:
  build:
    context: .
    dockerfile: nginx.dockerfile
  ports:
    - 80:80
  volumes:
    - ./wordpress:/var/www/html
  depends_on:
    - php
    - mysql
```

Let's break down everything that this block in the Docker Compose configuration is doing:

First, we're telling Docker to create a container with a name of **nginx**. It's going to be built from a local Dockerfile called `nginx.dockerfile`, found in the context element, which is set to the current directory.

Next, we're using the **ports** directive to tunnel the `:80` port from our local machine (the first number) to the `:80` port on the nginx container (the second number). This means that once active, listening to localhost:80 on our local machine will stream the response from our nginx container's 80 port.

After that, we're using the **volumes** attribute. This list is essentially a way to create symlinks between our local filesystem, and a Docker container's filesystem. The best part is that any changes to files in these folders on your machine or the container, mirror to one another. We're setting up a volume from our local `wordpress` folder, to the nginx container's `/var/www/html` directory.

Finally, this **depends_on** directive is a list of other services (or containers) that should be active and spun up *before* the current container is. Nginx depends on both PHP and MySQL to be ready and active, so those are the ones set here.

Okay, let's crack open that `nginx.dockerfile` and add the following contents to it:

```dockerfile
FROM nginx:stable-alpine

ADD ./nginx/default.conf /etc/nginx/conf.d/default.conf
```

Pretty simple, huh? We're doing all of two things here:

1. Setting up our container with the Docker Hub Nginx image, stable-alpine version. Alpine means that the base OS will be Alpine Linux, a light-weight container-friendly distro. 

2. Copying a local config file in our project's nginx folder, and replacing it over the default.conf file that exists in the `nginx/conf.d` folder.

Lastly for the Nginx portion of our dev environment, open up the `nginx/default.conf` file on your local system, and add in the following basic Nginx configuration that WordPress recommends:

```conf
upstream php {
    server unix:/tmp/php-cgi.socket;
    server php:9000;
}

server {
    listen 80;
    server_name wordpress-docker.test;

    root /var/www/html;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        include fastcgi.conf;
        fastcgi_intercept_errors on;
        fastcgi_pass php;
    }

    location ~* \.(js|css|png|jpg|jpeg|gif|ico)$ {
        expires max;
        log_not_found off;
    }
}
```

Alright, let's move on to the next part, MySQL!

## Adding MySQL

In our **docker-compose.yml** file, after our `nginx` service, let's add a new block for MySQL:

```yaml
mysql:
  image: mysql:latest
  restart: always
  ports:
    - 3306:3306
  environment:
    MYSQL_DATABASE: wp
    MYSQL_USER: wp
    MYSQL_PASSWORD: secret
    MYSQL_ROOT_PASSWORD: secret
```

This is all we need for our database. First, instead of building from a custom Dockerfile like our `nginx` container, we're using an image from the Docker Hub directly with the **image** attribute. In this case, the latest version of MySQL.

Next, we're setting this **restart** attribute to always, if for any reason the container goes down, we want it to spin back up on its own. Also like with the `nginx` container, we're exposing the default MySQL port (3306) from the container to our local machine.

Finally, the MySQL image expects a few environment attributes, which is a list of key value pairs, passed through on spin-up to the container. Using the above, we set the name and username of the database, as well as the password.

✨ Getting closer!

## Adding PHP

Like with `nginx`, we'll be building this service from a custom Dockerfile. Let's add the following to the bottom of our **docker-compose.yml** file:

```yaml
php:
  build:
    context: .
    dockerfile: php.dockerfile
  volumes:
    - ./wordpress:/var/www/html
```

With the PHP service, we don't need to expose any ports to our local system, since we really don't have any practical use for that. We are however, attaching the WordPress source files to the same directory that our `nginx` root is set to. 

Again, we're building from a custom Dockerfile, so let's open up `php.dockerfile` and add in the following contents:

```dockerfile
FROM php:7.4-fpm-alpine

ADD ./php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 wp && adduser -G wp -g wp -s /bin/sh -D wp

RUN mkdir -p /var/www/html

RUN chown wp:wp /var/www/html

WORKDIR /var/www/html

RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql
```

This Dockerfile is a bit bigger and more complex than the one we created for `nginx`. Let's break it down and see what's happening.

First, we're building the container from the PHP 7.4 FPM version, with Alpine Linux as the base. 

Next, we're copying over a local file, www.conf in the php directory, and replacing it with the default www.conf on the container.

After that, we're running a few commands to create a user called `wp`, and use them as the owner of the site root files. 

Finally, we're setting the working directory to the site root, and running the **docker-php-ext-install** command. This is a super helpful built-in command for this container, which as its name implies, installs PHP extensions and configures the necessary configuration files to use them. We're using that command to install mysqli, pdo, and pdo_mysql.

Okay, I think we're all done with the setup, so let's see if this runs!

## Starting Docker

We have all of our individual pieces in order, now it’s finally time to assemble our Docker network! Open up a terminal window and navigate to the root directory of this project. Then, run:

```bash
docker-compose up --build
```

Since this is our first time starting up these containers, and since some of them are using custom Dockerfiles (**php**, **nginx**), we include this `--build` option which is a shortcode for `docker-compose build`. Building the containers compiles custom images from the Dockerfile commands that we've specified.

After Docker pulls the images and runs the commands for each of your containers, they'll individually come online under an umbrella network named after your project folder. Then, the output for each of your containers will start streaming to the active terminal. 

**Note:** If you instead want to run this in the background and not see any console output, use `docker-compose up -d` to use detached mode.

After this completes, head over to your browser and navigate to `localhost`, and if everything worked successfully, you should see the WordPress installation screen!

## Installing WordPress

This step doesn't differ much than if you were installing WordPress on a server or typical local dev environment. Select your language, and then enter in your database credentials.

If you used the same environment variables in your **docker-compose.yml** file as the ones earlier, that would be

- **Database Name**: wp
- **User Name**: wp
- **Password**: secret
- **Database Host**: mysql

That last one is correct, `mysql` is the database host. **Why not `localhost`?**

Remember that each service (nginx, php, and mysql) are running in their own individual containers. The php service is handling the submission of this form and the processing of the WordPress source files. If we used `localhost` here, the PHP container would interpret that as itself, and look for a MySQL installation in its own container.

That doesn't exist, though. Our MySQL installation is in a separate container. But, because we're using Docker Compose and that umbrella network, the mysql container is available to every other container using its service name as the hostname, `mysql`.

Clicking submit, and you should be able to finish up the installation by adding in your site name and desired login information!

💥 Boom! We have a WordPress site running locally with Docker!

## Adding WP-CLI

There's a super helpful command-line tool for managing WordPress sites, plugins, and users, called [wp-cli](https://wp-cli.org/). As an addition to our local dev environment, I'd like to add this and use it with our site.

I could install it locally using their documentation, but if I don't have (or want to install) the dependencies for it on my local machine, I couldn't use it. Instead, let's add it with Docker and have our containers do the work for us.

First, we'll have to open up our **docker-compose.yml** file and add a new service at the bottom:

```yaml
wp:
  build:
    context: .
    dockerfile: php.dockerfile
  entrypoint: ['wp', '--allow-root']
  volumes:
    - ./wordpress:/var/www/html
```

Since this tool runs on PHP, we're building it off of the same Dockerfile that our php service is running on. We're also mounting the same volume as well, `wordpress` to `/var/www/html`. The only new thing on this block, is an attribute called **entrypoint**. 

Entrypoint is either a single string, or an array of strings, representing a command (and its arguments) that the container runs when spun up. We're replacing the default entrypoint of the PHP service from the Docker Hub image, with an array of two strings that compiles into the command `wp --allow-root`.

Using this is super easy, instead of having to ssh into a container like a VM, we use the `docker-compose run` command which expects a service name and a list of arguments that get passed to the entrypoint we just specified.

In our project root, we can get a list of all of the users on our WordPress site by running:

```bash
docker-compose run --rm wp user list
```

Which is equivalent to running `wp user list` inside of the container. 

An individual container is spun up using the php.dockerfile that we specified in the **wp** service. Then, the arguments are passed to the `wp --allow-root` entrypoint, and the output is streamed to our console. Finally, since we specified the `--rm` option, once the command completes, the container that ran it is destroyed and any memory is freed up.

All of this, with zero WordPress dependencies installed on our actual system!

## End of the Line

Well, there we have it! We’ve installed Docker, set up and configured a `docker-compose.yml` file, and built a LEMP stack of three containers wrapped in a single network. We've exposed ports on that network to access our website and database, and have even ran wp-cli commands using docker-compose’s `run` method.

Moving forward, if you want to bring down the containers and network, it’s as simple as navigating to your project’s root folder and running:

```bash
docker-compose down
```

This will both bring down, and destroy, the containers and **any associated non-volume data** that was stored in them.

Docker has made local development an absolute breeze for me, especially when it comes to juggling multiple WordPress projects spanning different versions. I can easily have one project running on a Docker network with a PHP container using `7.1`, and if I wanted to see how my current project would perform in PHP 8 it’s as simple as changing a **single character** in `php.dockerfile`, re-building, and bringing the docker-compose network back up.

I won't deny one caveat, you’ll get no better local development performance than running your stack directly on your machine’s hardware. I've weighed the tradeoff of **performance** for **versatility, ease of use, parallel environments, and customization**, and have decided it's just too handy to continue running dependencies like PHP and MySQL on my local hardware.

If you have any questions, comments, or want to chat more about 
PHP and web development in general, don’t hesitate to reach out to me on [Twitter](https://twitter.com/aschmelyun). I also run a monthly newsletter called [The VOLT](https://aschmelyun.substack.com), for info tid-bits and tips on PHP, JavaScript, and Docker-related development.