---
title: Using the magic of mkcert to enable valid https on local dev sites
slug: using-the-magic-of-mkcert-to-enable-valid-https-on-local-dev-sites
description: Mkcert is an incredible open-source command-line tool that generates trusted development certificates that you can use to enable https on local websites
categories: productivity
published_at: Sep 1 2020
excerpt: If you're a web developer, it's very likely you've used local dev sites to build your applications on. Something like example.test or mycoolsite.devlocal, right? When I'm spinning up a basic content site, I really don't pay attention to wrapping it up in https. However, when you start digging into more complex applications, especially those requiring registration and logins, https is useful and sometimes downright required depending on your frontend.
---

Want to skip the tutorial below and dig in for yourself? Check out the [repo](https://github.com/FiloSottile/mkcert) and follow along with the instructions on the README.

## Local https is awful

If you're a web developer, it's very likely you've used local dev sites to build your applications on. Something like `example.test` or `mycoolsite.devlocal`, right? When I'm spinning up a basic content site, I really don't pay attention to wrapping it up in https. However, when you start digging into more complex applications, especially those requiring registration and logins, https is useful and sometimes downright required depending on your frontend.

**Google Chrome (and most modern browsers), have taken a large stance against unencrypted http sites.** This *includes* dev sites and those that use development TLDs like `.test` and `.devlocal`. Usually you'll see a small exclamation point or some kind of notice in your address bar's left corner, but that changes when authentication comes into play. You'll see even larger notices about submitting information on an insecure website, and may even be blocked from performing that action.

A potential solution is "Let's create local self-signed certificates to enable https on our site". If we go [searching for tutorials](https://lmgtfy.com/?q=create+local+https+certificate) on how to accomplish that, there's literally a massive amount of options out there for every major operating system. The general consensus **if you're doing something like this from the command line, is using a tool like openssl**. Then, in your Nginx or Apache config file, enabling https, listening to the `:443` port, and referencing that file as your ssl certificate.

This worked, *kind of*. On my local dev app, whenever I'd navigate to the `https://` version, I'd be presented with a huge **"THIS SITE IS INSECURE"** full-screen pop-up. At first, it didn't bother me all that much. I could just click a "Continue to insecure site" button, my browser would remember my choice, and I could continue through the site. Albeit with a large red insecure https badge in the address bar, it still got the job done.

![Screenshot of Chrome giving an error that says your connection is not private for example.test](https://dev-to-uploads.s3.amazonaws.com/i/jdle2ctgfy7kd4xcipf6.png)

Then came the issue on newer versions of MacOS, where you wouldn't even *see* a "Continue to insecure site" bypass button on Chrome's popup. In order to actually *get it to appear*, you have to do [this awful process](https://stackoverflow.com/a/62379446) of saving the ssl certificate to your desktop, opening it up in Keychain Access, and manually setting the trust for it. And even then, **that just made the bypass button appear again**, still showing that large insecure badge on your site's address bar.

***There has to be a better way.***

## In walks mkcert

I honestly don't remember where I first came across [mkcert](https://github.com/FiloSottile/mkcert). It might have been on a Reddit post, Twitter thread, or random StackOverflow answer, but I am *so* glad that I did.

So, **what is it?** Mkcert is a command-line tool that does two things:

1. It generates a local certificate authority on your machine.
2. It creates self-signed ssl certificates *against* that authority.

What this means is that whenever your browser loads up a development site that uses one of its generated certs for `https`, it's validating that certificate against the dummy validation service installed on your machine. Therefore **faking your browser into thinking it's legitimate**.

It's magic!✨

## Getting set up

Installation of the actual utility is pretty straightforward, and the package is available on **Windows, MacOS, and Linux** platforms. I'll go through a brief overview of each, but for more detailed instructions I'd recommend checking out the [README](https://github.com/FiloSottile/mkcert/blob/master/README.md) on the official repo.

Let's get started!

**For MacOS using [Homebrew](https://brew.sh):**

```bash
brew install mkcert
brew install nss # only if you use Firefox
```

**For Windows using [Chocolatey](https://chocolatey.org/):**

```bash
choco install mkcert
```

**For Linux using [Linuxbrew](https://docs.brew.sh/Homebrew-on-Linux):**

```bash
brew install mkcert
```

💥 Bam! Now you have the tool installed on your system and ready to use in your terminal.

## Creating and using a certificate

If this is your first time using mkcert, you'll need to run it with the install flag. This only needs to be done once, and it creates the local certificate authority that we talked about earlier.

Just open up your terminal, and run:

```bash
mkcert -install
```

You should see the following appear if everything went successfully:

```bash
Created a new local CA at "/Users/andrew/Library/Application Support/mkcert" 💥
The local CA is now installed in the system trust store! ⚡️
The local CA is now installed in the Firefox trust store (requires browser restart)! 🦊
```

Now that we have our authority installed, **we can create an actual certificate**. I recommend first navigating in your terminal to your project's directory, maybe even creating a new directory called `mkcerts` or something similar.

Then, it's just a matter of running the command:

```bash
mkcert example.test
```

Replacing `example.test` with whatever local domain you're using to display your site on.

You can also use IP addresses, or even **wildcard subdomains**. Chaining them together in the same call, if you'd like one certificate for multiple different domains on one site:

```bash
mkcert example.test "*.example.test" 127.0.0.1
```

And if everything goes well, you should have two new files in the directory you ran that command in, `example.test.pem` and `example.test-key.pem`. Let's use them!

All we have to do is **make them accessible to our Apache or Nginx config files**, and use them like we would an actual certificate from Let's Encrypt or another authority.

In **Nginx**, alongside a prepared ssl block, that might look like:

```conf
server {
    listen  443 ssl;
    server_name  example.test;
    root  /Users/andrew/Sites/example.test/public;
    ssl_certificate     /Users/andrew/Sites/example.test/mkcerts/example.test.pem;
    ssl_certificate_key /Users/andrew/Sites/example.test/mkcerts/example.test-key.pem;
}
```

And in **Apache**:

```conf
<VirtualHost 127.0.0.1:443>
ServerAdmin webmaster@example.test
DocumentRoot /Users/andrew/Sites/example.test/public
ServerName example.test
SSLEngine on
SSLCertificateFile /Users/andrew/Sites/example.test/mkcerts/example.test.pem
SSLCertificateKeyFile /Users/andrew/Sites/example.test/mkcerts/example.test-key.pem
</VirtualHost> 
```

All that's left to do is restart the webserver process, and navigate to your example site with `https://` in your browser of choice. You should then be presented with a **wonderful green secure badge** in the address bar! ✅

## That's all folks

Hopefully after going through this article and trying out mkcert yourself, you've been converted to the **easier way of creating and using self-signed ssl certificates** to enable https on your development websites.

If you have any questions about anything covered in the above, concerns or problems getting started with mkcert, or just want to chat about web development topics in general, feel free to reach out to me in the comments or on my [Twitter](https://twitter.com/aschmelyun). 