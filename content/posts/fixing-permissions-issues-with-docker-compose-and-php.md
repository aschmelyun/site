---
title: Fixing permissions issues with Docker Compose and PHP
slug: fixing-permissions-issues-with-docker-compose-and-php
description: My fairly elegant solution to fixing the file_put_contents and RuntimeException errors with local PHP Docker development environments.
categories: docker, php
published_at: Nov 29 2021
excerpt: I've been maintaining and iterating on a basic Docker Compose setup for Laravel over the last year or so. It's worked well enough for local development, which was what I originally intended it for. Ever since I released it though, I've had multiple people sending me concerns and GitHub issues surrounding permissions problems.
---

I've been maintaining and iterating on a basic [Docker Compose setup for Laravel](https://github.com/aschmelyun/docker-compose-laravel) over the last year or so. It's worked *well* *enough* for local development, which was what I originally intended it for. Ever since I released it though, I've had multiple people sending me concerns and GitHub issues surrounding permissions problems.

I've tried multiple different fixes over the course of the repo's lifetime, but only recently have I found a solid solution that seems to work with a variety of common platforms and OS's, both locally and in production.

**So, let's dive into it!**

## The Problem

Before we get into the solution, I'm going to give a brief overview of the issues, and what the root  cause is for them. Feel free to **skip this** if you'd like to head straight into the fix!

Alright, let's move on.

I'd start by getting my Docker Compose files together, and setting up my Laravel application in the right directory. After spinning up the container network with `docker-compose up -d`, everything in the terminal would return okay and I didn't see any errors pop up during the build.

Opening up my browser and visiting the site though, would lead to something like this:

![A screenshot of a Laravel error page that says The stream or file "/var/www/html/vpa/storage/logs/laravel.log" could not be opened in append mode: failed to open stream: Permission denied](https://i.stack.imgur.com/1gVDe.png)

I'd also come across a similar error when trying to use any `artisan` or `composer` commands in the browser through the Docker container(s). Making this even more frustrating, visiting direct images or compiled assets would return just fine.

It turns out that the reason this was happening is because the **PHP container didn't have the correct permissions** to write to the filesystem that my Laravel app's files were under. But, *why*? It shouldn't really make sense considering that Docker containers are closed, isolated systems. 

That's half true, and the big difference lied in how I was bringing the app data into the containers.

In Docker, there's two main ways of bringing data into a container:

**The first is by using ADD/COPY** commands in Dockerfiles. These take a file or folder contents and copy them to a specified directory in a container at build time. The biggest pro with this is portability, since you don't have to distribute your application's source files, they're all included inside a Docker image. Changes made to any of your application's files though, can't be easily accessed by the local machine for development.

**The second is by using volumes.** Volumes act as a sort of symlink between a local file or folder on your host machine, into a file or folder inside of the container. The biggest pro with this method is that any changes made to those files is reflected on both sides of the volume. This makes it great for development environments where source code is changing rapidly.

The setup that I linked at the top of the article uses the second method. Docker Compose and mounted volumes bring my site's data and files into the container so that the application can be displayed in a browser. That way, I'm also able to develop an app and make changes to those same files locally, having them reflect in the browser instantaneously. 

**But, there's a catch.**

Whenever the volume gets mounted to the container, the file and directory ownership from the *host* system passes along to the *container* as well. 

Why is that a problem? Let's say that you're running a Docker instance on your local machine, and your app's files are owned by a user called **andrew** with an ID of 502. Well, the PHP container is running `php` on a user called **www-data** that has an ID of 1001. When trying to modify files under the application's directory, like writing to the cache or saving an image, the difference in those two ownerships causes a conflict in the permissions and the process errors out.

The fix seems simple enough. Just ssh into the container and chmod the application files with the correct permissions!

Except, it's not permanent. If you bring down the containers and spin them back up, the permissions are reset and you'll have to do it again.

Okay, so let's add a command to the Dockerfile to do it for us, each time that the containers are built!

Unfortunately, that won't work either. The volumes are mounted *after* the container build process runs, so no matter what commands you have in the Dockerfile, they won't affect files mounted by volumes in Docker Compose.

I needed a fix that was reliable, autonomous, and most importantly, replicable.

## The Fix

If I couldn't directly affect the files that were being added into the container, the best thing that I could do would be replicate the permissions that they were using *inside* of the container.

For the PHP containers, I'm building them out with a custom Dockerfile that's using the [php-fpm-alpine](https://hub.docker.com/_/php?tab=tags&page=1&name=fpm-alpine) image as a base. From there, I'm creating a group called `laravel` *with the same group id as my local machine's group that owns my app's files*. **This is important.** I also do the same thing with the user, creating a user called `laravel`. 

Those commands look like this:

```docker
RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel
```

Since this is running off of Alpine Linux, it's a little different than say, Ubuntu. But here's a brief breakdown of what each command does:

- **RUN addgroup**
    - `-g` pass in a group ID that we want to attach to this new group
    - `${GID}` an environment variable for the group ID passed in through Docker Compose (I'll get to this soon)
    - `--system` it's a system-wide group
    - `laravel` the name of the group we're creating
- **RUN adduser**
    - `-G` the name of the group we want to assign this user to, an in our case it's the group we just created
    - `--system` it's a system-wide user
    - `-D` don't create a password for this user
    - `-s /bin/sh` give it the Alpine Linux shell
    - `-u ${UID}` pass in a user ID that we want to attach to this new user, and like the group ID it's coming through an environment variable
    - `laravel` the name of the user we're creating

So I created a group and user called laravel, both of them are getting specific IDs, and I attached them to each other. But, I needed to modify the user that PHP is actually running on in the container, since by default it's using `www-data`. I *could* copy over a modified php.ini file, but since it's just a few character changes I decided to use a couple of commands in that same Dockerfile:

```docker
RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
```

This just uses **sed** to replace two lines that define the user and group PHP runs as, with the user and group we just created, **laravel**. The IDs that we need are the IDs of the app file's owner, so I had to find that out.

In my terminal I can run `id -u` and `id -g` to get both of those, assuming that the current user logged in is who owns the files for the Laravel app I'm working on.

![Screenshot of iTerm2 with the commands id -u returning 501 and id -g returning 20](https://dev-to-uploads.s3.amazonaws.com/uploads/articles/vhe0epdc30q0ic9qnc5i.png)

Alright, **501** and **20**. 

Back in the PHP Dockerfile, I need to create those environment variables so that when the image is built, it uses those two IDs. At the top, I add this:

```docker
ENV UID=501
ENV GID=20
```

But wait, I don't *really* want to hard code these values in. Especially since I'm open sourcing this starter kit, there's no telling what group and user ID will be needed!

We can get around that by using arguments instead, and passing them up to the docker-compose.yml file. So instead of the above, I used this:

```docker
ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}
```

This might look weird and repetitive, but it's how we can get Dockerfiles being built with Docker Compose to use something called **arguments**. Which, *those* in turn are used in the `docker-compose.yml` file like this:

```yaml
php:
    build:
      context: .
      dockerfile: php.dockerfile
      args:
        - UID=${UID:-1000}
        - GID=${GID:-1000}
```

Again, there's more repetition, but it boils down to this: The arguments UID and GID are both using environment variables pulled from the terminal. If a UID or GID isn't found, value for each is set to 1000 (that's what the `:-` separator means, a default value).

Okay, so **how does this come together?**

In the terminal, we'll first have to check if those environment variables exist by running `echo $UID` and `echo $GID`. If you see values for both, great! If you don't, you'll need to export them by running the following command:

```bash
export UID=$(id -u) && export GID=$(id -g)
```

Then, all you have to do is build and bring up your containers like usual!

```bash
docker-compose up -d --build
```

Your PHP container will be built and during that time a laravel group and user will be created with **your user ID and group ID**. The `php` process will then use the new laravel user to run as, meaning that any write access to the app filesystem should be granted since the defining user attributes for the permissions now match.

## Wrapping Up

Permissions with Docker and PHP are a fairly complex beast. It doesn't help that MacOS doesn't seem to be affected by this, due to the virtualization layer that exists between the native operating system and the Docker software. There's also issues between Windows with and without WSL-2, and Ubuntu. 

I've tested the above in all major operating systems and platforms, both for local development and in production environments, and haven't had any permissions issues crop up yet. Additionally, after publishing these changes up to the GitHub repo, the issues section has been a bit quieter. 

Something to note though, is that if you're using a production or local environment as the root/root user, you're going to have problems with the steps above. First, it's recommended that any production system uses a non-root sudo user, but if you decide to continue on that path, I've created a separate section in the [README](https://github.com/aschmelyun/docker-compose-laravel/blob/main/README.md) of the GitHub I linked at the beginning which should help you with any problems you have.

If you have any questions on anything in this article, or about using Docker with Laravel and PHP in general, feel free to reach out to me on [Twitter](https://twitter.com/aschmelyun)!