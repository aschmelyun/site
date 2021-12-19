---
view: layout.post
title: Crafting a better local Laravel dev environment with Docker
cover_image: https://dev-to-uploads.s3.amazonaws.com/i/h0781s45mpqbqv4jyfgp.jpg
description: Create a Docker network for Laravel development without requiring Composer, NPM, or PHP to be installed on your local machine
categories: laravel, docker
published: Feb 19 2020
excerpt: This tutorial is built on a previous one that I wrote a few months back called The beauty of Docker for local Laravel development. While this article is beginner-friendly, it leaves out a lot of the original setup for the nginx, php, and mysql containers. I'd recommend that you start off with the previous tutorial first, and then move on to this one.
---

## tl;dr

If you'd like a more visual aide, I've published [a video](https://www.youtube.com/watch?v=I980aPL-NRM) which follows along with this tutorial in depth. It shows you all of the steps, and explains how best to use the above commands in the finished running network.

Want to skip the detailed tutorial altogether and dive into this yourself? Install [Docker](https://docs.docker.com/docker-for-mac/install/) for your OS, clone [this repo](https://github.com/aschmelyun/docker-compose-laravel), add your Laravel app files to the **src** directory, and from the root project directory you just cloned run:
`docker-compose up -d --build`.

You can then use Composer, NPM, and Artisan commands like so:

```shell
docker-compose run --rm composer require aschmelyun/larametrics
docker-compose run --rm npm install --save vue
docker-compose run --rm artisan migrate
```

## Some backstory

This tutorial is built on a previous one that I wrote a few months back called **[The beauty of Docker for local Laravel development](https://dev.to/aschmelyun/the-beauty-of-docker-for-local-laravel-development-13c0)**. While this article is beginner-friendly, it leaves out a lot of the original setup for the nginx, php, and mysql containers. I'd recommend that you start off with the previous tutorial first, and then move on to this one.

After producing that original tutorial and uploading the Docker repo to GitHub, I started receiving regular questions from the community at large about how to do certain things within the container network. The biggest ones could be boiled down to:

> "How do I run {Composer|NPM} if I don't have {PHP|Node} installed on my local machine?"

Unfortunately, I had to come back with an *"I don't really know right now, but I'll find out!"*. I did some digging, and just 48 hours later I had a solution that worked. At least, for every use case I regularly use when developing Laravel applications, and without having to have anything besides Docker installed on my computer.

This will expand on the previous docker-compose.yml file and add three new containers, in addition, you'll learn how to easily run Composer, NPM, and Artisan commands on your console through the container network.

**Let's get started!**

## Adding Composer

Starting off easy, this container is made from a pre-built image available on the Docker hub. Before we make any changes, if your Docker network is up, bring it down with `docker-compose down`. Let's open up our `docker-compose.yml` file, and add this block defining the Composer container under the last service:

```yml
  composer:
    image: composer:latest
    container_name: composer
    volumes:
      - ./src:/var/www/html
    working_dir: /var/www/html
    depends_on:
      - php
    networks:
      - laravel
```

Most of these headings and values should be pretty familiar to you, however there's one that sticks out as new for this container: **working_dir**.

See, by default, the Composer container expects the site files to be available in a particular directory (I think this is /data by default, but I'm not 100% sure). Now, we could just change the volume to mount at `/data` instead of `/var/www/html`, but I like keeping things synced up across my container network. Instead, working_dir lets us overwrite the default directory Composer looks in, replacing it with where our application's files are actually located.

If we bring our container network back up with `docker-compose up -d`, we can see that the Composer container is built alongside our others and brought up as expected. **Now, here's where the fun happens.**

Using the following command, we can tell Docker to spin up the Composer container, and run a command with the same namesake as the service (e.g. composer), inside of it. Then, once the job has completed, the container is brought back down and any memory freed up. That command is:

```shell
docker-compose run --rm composer require aschmelyun/larametrics
```

Of course, you can replace `require aschmelyun\larametrics` with any other Composer command you want to run, but the point is that this all takes place isolated inside the container, makes the necessary changes to your application's file structure, all without having to have PHP or Composer installed locally.

Let's keep it going!

## Adding NPM

Our NPM container is almost just as simple as Composer, but with another new addition. Take a look at the following block that defines the NPM service. Bring your container network down, and add this new service under the Composer one created earlier:

```yml
  npm:
    image: node:latest
    container_name: npm
    volumes:
      - ./src:/var/www/html
    working_dir: /var/www/html
    entrypoint: ['npm', '--no-bin-links']
    networks:
      - laravel
```

We've defined the service as the name of the command we'll be running with it, and just like Composer have built this off of an image available on the Docker Hub (the latest version of Node). Also just like Composer, we've overwritten the default folder structure that npm looks for using the `working_dir` key and setting it to the application root.

Below that though, is our new option, **entrypoint**. Like we mentioned earlier, when you run a command with `docker-compose run`, it runs the command with the same namesake as the service name (npm will run npm, composer will run composer, etc).

But sometimes we need to specify the path to the command, add additional parameters, or even change the command name entirely. This is exactly what entrypoint is used for!

It accepts an array with the first element being the command you'd like to run, and each subsequent items get taken in as flags or parameters that you'd like to set. For our npm service, I had some trouble getting it to run smoothly, so adding the `--no-bin-links` flag seemed to help that. Using the entrypoint I can easily add this in as an option with a second array value.

If we bring our container network back up with `docker-compose up -d`, we can run npm commands using the same syntax as earlier:

```shell
docker-compose run --rm npm install
docker-compose run --rm npm run dev
```

The npm container spins up with a Node image, `npm install` or `npm run dev` is ran at our application's root, and the output is streamed to our console just like if it was happening on our local machine. Once it's finished the container is brought back down, and our compiled assets are ready to go.

One more to go!

## Adding Artisan

In my opinion, this has been the best addition to my workflow for local Laravel development with Docker. Using Artisan commands with Docker ensures that I'll always be able to upgrade my PHP version to match the latest requirements from Laravel, without having to have it installed on my local machine. Additionally, if something really gets stuck, it's just a few seconds to restart the containers vs finding my local PHP process and restarting it (or my computer).

While this isn't going to be built from a Docker Hub image, Artisan runs on PHP, and we already have a working configuration for our PHP service, so we can just use that! Bring your container network down and take a look at the block below which configures our Artisan service. Add it to the bottom of your `docker-compose.yml` file:

```yml
  artisan:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: artisan
    volumes:
      - ./src:/var/www/html
    depends_on:
      - mysql
    entrypoint: ['/var/www/html/artisan']
    networks:
      - laravel
```

While there's nothing new in here that hasn't been explained already, I'll break it down a little anyway, just for a more thorough description. Instead of using the `image:` key earlier, we're using `build:` which takes in a context (the current directory) and a dockerfile that we want to build this container off of. The one in question is the same I've previously used to define our PHP service, and it's incredibly simple.

For the entrypoint, I've had to make an addition. The reason for that, is this: If we were to exclude the entrypoint and run the container using `docker-compose run --rm artisan migrate`, it would fail because the `artisan` command isn't installed as a global throughout the container. It's a single file, available in the project's root directory.

By adding in an entrypoint with the full path to the artisan script as the single value in its array, we're basically telling the container to alias `/var/www/html/artisan` as just `artisan`.

Let's bring our containers back up a final time with `docker-compose up -d --build` (since the new artisan container is created with a dockerfile), and test out a few artisan commands:

```bash
docker-compose run --rm artisan migrate
docker-compose run --rm artisan key:generate
```

💥Boom💥

## All Set!

It's really that simple to get a much better local Laravel dev environment setup with Docker and docker-compose. I've been tweaking and working with this setup for over half of a year now, and I've absolutely been loving it. **I honestly don't ever think that I'll go back to having a locally-installed LAMP stack running on my machine again.**

Again, if you want to just check out the source code, you can find it in the [GitHub repository here](https://github.com/aschmelyun/docker-compose-laravel), specifically check out the `docker-compose.yml` file or clone the repo and add your application's files to the **src** directory.

Finally, if you have any questions at all or would like help with this, or any other web development-related topics, please feel free to follow me or message me on [Twitter](https://twitter.com/aschmelyun).

Thanks for reading!