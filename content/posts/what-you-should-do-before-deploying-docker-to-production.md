---
title: What you should do before deploying Docker to production
slug: what-you-should-do-before-deploying-docker-to-production
description: A concise guide to show how to go from local development to production with Docker, including deployment workflows and CI/CD.
categories: docker
published_at: Nov 29 2023
excerpt: If you're starting on the journey from local development to production with Docker and don't know all the steps involved, this guide is for you. We'll go over creating production Dockerfiles, tweaking configurations, setting up deployment workflows, and adding seamless CI/CD integration.
---

If you're starting on the journey from local development to production with Docker and don't know all the steps involved, this guide is for you.

We'll go over some of the intricacies of the transition, like creating production Dockerfiles, and tweaking configurations. Then we'll work on deployment workflows, and finish off with CI/CD. By the end we should have seamlessly moved your Docker application out into a stable, live environment.

Let's get started!

## Create a dockerignore file

Before we start actually building Docker images and deploying containers to production, we need to ensure that we are _only_ adding files to them that are necessary. A lot of the time when we're developing locally, we have files and directories that aren't needed in production and take up pretty significant space. Things like vendor directories and of course the infamous `node_modules`.

If you're familiar with .gitignore that's used in version control, a `.dockerignore` file works pretty much the same way. Specifying patterns for files and directories ensure they're ignored when building a Docker image. This way, we can reduce the overall size of the final image and improve the build performance.

Here's a generic example of what you might include in a dockerignore file:

```dockerignore
node_modules/
npm-debug.log
yarn-debug.log
yarn-error.log
.docker/
.vscode/
.idea/
.DS_Store
```

This is a good start, and covers a broad range of files often found during development, but not necessary for a production environment. 

Let's try something a little more specific.

If we're building an image for a Laravel application, our dockerignore file should have some additional files and directories added to it.

```dockerignore
.env
bootstrap/cache/
storage/
vendor/

# ... additional base files and directories shown above
```

And a dockerignore file for a Next.js app container will have other files and directories to be ignored.

```dockerignore
node_modules/
out/
.cache/
.next/

# ... additional base files and directories shown above
```

Files and directories specific to the framework or language you're working in can be ignored on a per-project basis. Alternatively, you can create a _single_ dockerignore file that contains ignores for _multiple_ frameworks and languages. It's up to your preference.

## Create production config files

In our local development environment, our application might rely on specific settings and configurations, such as debugging options, development databases, or less restrictive security settings. However, in a production environment, these configurations need to be fine-tuned for optimal performance, security, and reliability.

To address these differences, we can create distinct versions of configuration files tailored for our production deployment. This can include new config files for web servers like Nginx or Apache, PHP settings, and any other files that vary between a dev and prod environment.

Let's take a look at a few quick examples.

When prepping for a production image, we should make sure that our config file is pointed to the live URL, and contains info for SSL/HTTPS configurations

```nginx
server {
    listen 443 ssl;
    server_name my-prod-domain.com;

    location / {
        proxy_pass backend:9000;
    }

    ssl_certificate     my-prod-domain.com.crt;
    ssl_certificate_key my-prod-domain.com.key;
}
```

For PHP, you might adjust settings related to error reporting, execution time, log paths, and caching.

```ini
display_errors = Off
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
log_errors = On
error_log = /var/log/php/error.log

max_execution_time = 30 
max_input_time = 60
memory_limit = 256M
post_max_size = 20M
upload_max_filesize = 20M

opcache.enable = On
```

Once you've crafted production-ready configurations, the next step is to integrate them into your Docker images. This involves copying these files into the appropriate locations within the image during the build process.

## Build the production images

Now that we've fine-tuned our production config files, the next step to focus on is building dedicated Docker images specifically tailored for production use.

If you haven't already, create a Dockerfile for your production deployment. If you're using a pre-built image from the Docker Hub during development, you'll have to start from scratch. Otherwise, it's okay if you just copy your development image to a new file.

I usually call mine something like `local.Dockerfile` and `prod.Dockerfile`.

The first thing we need to do is establish a light-weight base image, and add in our config files from earlier.

```dockerfile
# Use a minimal base image suitable for production, alpine is good for this
FROM nginx:alpine

# Copy production configurations
COPY nginx-production.conf /etc/nginx/conf.d/default.conf
COPY php-production.ini /etc/php/8.2/fpm/conf.d/production.ini
```

Unlike local development where Docker Compose volumes might be convenient, production images should store the application's codebase in its entirety. So we'll need to include code to add in our source, that way the final image is entirely self-contained.

```dockerfile
# ... previous content from above

# Copy the application source code
COPY . /var/www/html
```

Commands, entrypoints, and exposed ports are likely to remain the same between development and production.

With our Dockerfile(s) prepared, it's time to build the production images. We can use the docker build command, specify the path to the directory containing the Dockerfile, and tag the image with a name and version. This will help us differentiate images locally and make version control easy.

In our terminal, we can run the following.

```bash
docker build -t my-app:1.0.0 -f prod.Dockerfile .
```

If we run `docker image ls` we should be able to see our newly-built image in our local registry. We can spin it up by running `docker run my-app`, which should start up our production image.

At this point we'll have to consider _where_ we're going to store our image. We could simply upload it to the server each time we want to use it, but it's more practical to use a service that specializes in Docker image storage.

These are called container registries, and we use them to store and retrieve Docker images in a secure place. This step is optional, we could simply upload an image to the server each time we want to use it, but can be super helpful for versioning, collaboration, and deployment automation.

Some recommended options would be the [Docker Hub](https://docs.docker.com/registry/) or [GitHub's Container Registry](https://docs.github.com/en/packages/working-with-a-github-packages-registry/working-with-the-container-registry).

After setting up an account with one of the above, we can push our production image up to our registry using the following command.

```bash
docker push your-registry-url/my-app:1.0.0
```

> Note: Replace your-registry-url with the URL that was provided by your container registry.

With your production images built and optionally pushed to a registry, you're one step closer to deploying your Dockerized app to a production environment!

## Modify the Docker Compose file

It's likely that during development (and likewise, production), you have multiple services orchestrated together. Usually this is accomplished using something like the built-in Docker Compose, and if that's the case we'll need to prepare it for use in production.

If that's the case, go ahead and copy your `docker-compose.yml` file to `docker-compose.prod.yml`. This will be our production file used in deployments.

First, replace any image with the ones we built earlier.

```yaml
version: '3'
services:
  app:
    image: my-registry-url/my-app:1.0.0
    # ...other config options

  mysql:
    image: mysql:latest
    # ...other config options
```

Unlike local development environments where volume mounts can be convenient for live code reloading, in production, it's advisable to remove volume mounts from the Docker Compose configuration. This ensures that the production environment relies solely on the images built during the previous step, making the deployment more predictable and replicable.

```yaml
version: '3'
services:
  app:
    image: my-registry-url/my-app:1.0.0
    volumes:
      - ./src:/var/www/html # remove me!
      - ./logs:/var/logs # remove me!
    # ...other config options
```

It's likely that you'll also have different environment variables for production applications vs ones in your local development environment. Details like server credentials, third-party API keys, and debug levels are probably different going into production.

Instead of adding a production env file into your Docker image, we can keep it safer by using the `env_file` attribute in our production docker-compose.yml file.

```yaml
version: '3'
services:
  app:
    image: my-registry-url/my-app:1.0.0
    env_file: .env.prod
    # ...other config options

  mysql:
    image: mysql:latest
    # ...other config options
```

All that's left to do is get this file onto our server, and run `docker-compose up -d` to get our full stack up and running!

As a quick aside, using Docker Compose for container orchestration in production is _totally fine_, and I've run multiple side projects with moderate traffic. However, it doesn't fit every need, so it might be worth exploring other options like Kubernetes if you think you'll need a more powerful setup.

For the final section, we'll delve into setting up an automated workflow to ensure a smooth and efficient deployment process for your Dockerized application.

## Set up a deployment workflow

Now that our production Docker containers are up and running on a server, it's a good idea to get a solid strategy for managing and deploying changes to our application.

We can take a manual approach, where we rebuild and tag a new Docker image each time there's a code change. What does that look like?

First, we build and tag a new image, and (optionally) push it up to our registry.

```bash
docker build -t my-app:1.1.0 -f prod.Dockerfile .
docker push your-registry-url/my-app:1.1.0 
```

After building the new image, we'll need to restart the services on the server. This should also pull in the latest image as well.

```bash
docker-compose -f docker-compose.prod.yml up -d --no-deps
```

While this manual approach is straightforward, it becomes impractical as your application grows in complexity, the frequency of updates increases, or you're working on a team of more than a few people.

To streamline this process, we can leverage a CI/CD platform like GitHub Actions. This enables automation of the build and deployment workflow whenever changes are pushed to your repository automatically.

Let's take a look at an example basic workflow to accomplish what we did above.

```yaml
name: CI/CD

on:
  push:
    branches:
      - main

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout Repository
        uses: actions/checkout@v2

      - name: Build and Push Docker Image
        run: |
          docker build -t my-app:${{ github.sha }} -f prod.Dockerfile .
          docker push my-registry-url/my-app:${{ github.sha }}

      - name: SSH into Production Server and Update Containers
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.PRODUCTION_SERVER }}
          username: ${{ secrets.PRODUCTION_SERVER_USERNAME }}
          key: ${{ secrets.PRODUCTION_SERVER_KEY }}
          script: |
            docker-compose -f /path/to/your/docker-compose.prod.yml up -d --no-deps
```

This example workflow does the following:

1. Waits for a code push or merge into the main branch. 
2. Checks out the code.
3. Builds and pushes a new Docker image with a unique tag based on the commit SHA. 
4. SSH's into the production server and updates the containers with the latest images.

Sensitive information, like production server details, is stored securely as secrets in the GitHub repository.

By using automated CI/CD, we've created a streamlined and efficient deployment process, making it easier to manage updates and changes to the Docker images and application in general.

## Next steps

That's about it! You've learned what it takes to modify a local Docker setup to something production-ready, and deploy it out on a live server. As you continue to enhance and maintain your Dockerized application, here are some key considerations and next steps:

While we've touched on using environment variables and .env files for configuration, you can utilize third-party  services for secret management, such as [HashiCorp Vault](https://www.vaultproject.io/) or [AWS Secrets Manager](https://aws.amazon.com/secrets-manager/). These will allow you to store and manage secrets separately from your Docker Compose files, and enhance the overall security of your application.

While I showed off a companion MySQL service with our production Docker Compose setup, you can explore external database services as an alternative.

I mentioned earlier that mounted volumes should be removed for production, however there are instances where you need to share data between containers or store things like images on a server outside a container for posterity.

For that, we can use a Docker volume, ensuring that the path we're mounting is in the scope of the use case.

```yaml
services:
  app:
    image: my-app:1.0.0
    volumes:
      - my_images:/var/www/html/storage/app/images

volumes:
  my_images:
    driver: local
```

Remember, the journey doesn't end with deployment!

Embrace a continuous improvement mindset, stay informed about new advancements in Docker and containerization in general, and adapt new deployment strategies as your application evolves.