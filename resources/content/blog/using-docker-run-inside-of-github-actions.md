---
view: layout.post
title: Using Docker Run inside of GitHub Actions
description: With a specific action added in your job, you can use docker run to fire off singular containerized processes during one of your deployment steps.
categories: docker, devops
published: Jan 10 2021
excerpt: Recently I decided to take on the task of automating my site's build and deployment process through GitHub Actions. I'm using my static site generator Cleaver to handle that, which requires Node + PHP to be installed in order to run the build process. Actions supports both of those runtimes out of the box, but I had just created a perfectly good Docker Image for using Cleaver, and wanted to use that.
---

Recently I decided to take on the task of automating my site's build and deployment process through [GitHub Actions](https://github.com/features/actions). I'm using my own static site generator [Cleaver](https://github.com/aschmelyun/cleaver) to handle that, which requires both Node + PHP to be installed in order to run the asset compilation and build process. Now, GitHub Actions _supports_ both of those runtimes out of the box, but I had just created a perfectly good [Docker image](https://github.com/aschmelyun/cleaver-docker) for using Cleaver, and instead wanted to use that.

Ultimately it was a mixture of just wanting the fine-grain control that a single Docker image provides, and because, well **I just wanted to see how to do it!**

## What Didn't Work

So, you're able to actually use Docker images in GitHub actions, but by default you're only able to use them one of two ways.

```yaml
jobs:
    compile:
        name: Compile site assets
        runs-on: ubuntu-latest
        container:
            image: aschmelyun/cleaver:latest
```

This first option is as the base for an entire job. Normally a lot of GitHub actions have you start off with an Ubuntu distro as the base for the VM (there are other OS's you can choose from as well) and then add in your container image. But the entire rest of the job uses _whatever container you specify_ as the starting point for **all** of the rest of the job's steps.

```yaml
jobs:
    compile:
        name: Compile site assets
        runs-on: ubuntu-latest
        steps:
          - name: Run the build process with Docker
            uses: docker://aschmelyun/cleaver
```

This second option is as an action in the steps for a job. Instead of something like `uses: actions/checkout@v2`, you can instead specify a Docker image from the hub to run in its place. The problem with this one though is that you have to generate a Docker image that runs **specifically like a GitHub action expects**. That means things like avoiding `WORKDIR` and `ENTRYPOINT` attributes, as they're handled internally by the GitHub Actions worker.

What I wanted was simply to be able to use `docker run ...` under a _single_ action in a job.

## What Worked

I ended up finding an action available on GitHub by **addnab** called [docker-run-action](https://github.com/addnab/docker-run-action) that works exactly how I wanted. You specify an image, any options, and a list of commands to run with it, and only during that step of the build process is it used.

```yaml
jobs:
    compile:
        name: Compile site assets
        runs-on: ubuntu-latest
        steps:
          - name: Check out the repo
            uses: actions/checkout@v2
          - name: Run the build process with Docker
            uses: addnab/docker-run-action@v3
            with:
                image: aschmelyun/cleaver:latest
                options: -v ${{ github.workspace }}:/var/www
                run: |
                    composer install
                    npm install
                    npm run production
```

Let me break down what each of these lines does:

```yaml
image: aschmelyun/cleaver:latest
```

This one is pretty obvious, it specifies the image that's pulled and used in the docker run command. I'm using mine for Cleaver that's on the public [Docker Hub](https://hub.docker.com/r/aschmelyun/cleaver), but you can also use a privately-owned image as well.

```yaml
options: -v ${{ github.workspace }}:/var/www
```

Here I'm creating a bind mount from the current workspace to `/var/www`, which is the working directory that my Docker image expects. `github.workspace` includes all of the code checked out from our current repo, and I'm mounting that whole directory as that's what my build process expects. Because I'm using a bind mount, **anything done to this code will then be available to GitHub Actions** in any following step (like a deployment).

```yaml
run: |
    composer install
    npm install
    npm run production
```

This is where I specify the actual commands I want to run against my container image. This action **ignores the entrypoint of the container image**, so even though normally using `docker run aschmelyun/cleaver:latest` it would run those three commands, using this action I have to actually specify them out again in the yaml. 

Once they complete, GitHub should now have a new `dist` folder in the workspace containing the compiled site assets that can then be deployed out to a production server. Once the job finishes up, that's removed and is never committed to the repo or accessible to a separate job.

## Wrapping Up

Sometimes during a CI/CD process it's helpful to use a ready-made Docker image to run one-off commands and processes. This could be especially helpful if the software you need isn't available on the actions platform, or requires a lengthy setup process that's already written out in a Dockerfile.

If you have any questions about anything in this article, or if you'd like to get more smaller pieces of regular content regarding Docker and other web dev stuff, feel free to follow or reach out to me on [Twitter](https://twitter.com/aschmelyun)!