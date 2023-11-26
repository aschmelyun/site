---
view: layout.post
title: Intercept and inspect http requests with reqon
description: Reqon is a locally installed and fully open source cli tool to capture and inspect http requests in real-time.
categories: tools,productivity
published: Sep 18 2022
excerpt: A few weeks ago I had an itch to scratch, I was working with a legacy application that was sending out a ton of curl requests to a production service. The problem was, I had no idea what was in them. I could have dug through the spaghetti and documented each one, but I needed an answer pretty quickly.
---

A few weeks ago I had an itch to scratch, I was working with a legacy application that was sending out a ton of curl requests to a production service. The problem was, _I had no idea what was in them_.

Sure, I could have dug through the spaghetti and documented each one, but I needed an answer pretty quickly and instead just started `var_dump`ing the requests to local text files. I thought, "there's gotta be a better way than this", and figured if I could just change the external API endpoint to something local, I could capture the requests and check them out in real-time.

After searching for a while I found a few paid and hosted services that offer what I wanted, but nothing that was local or open source. So, I decided to make it! What I came up with, is **reqon**.

![Screenshot of reqon running in a terminal window](https://raw.githubusercontent.com/aschmelyun/reqon/main/art/og_image.png)

## Installation

You'll need to have npm installed and a node version of 16.0.0 or higher. Then, in your terminal just run:

```bash
npm install -g reqon
```

That's it!

## Usage

Run the `reqon` command in your terminal to start listening for requests on a local server. There's a few different options you adjust to your liking.

```
reqon [options]

options:
  --port=<port>             sets the port to listen for incoming requests
  --dashboard-port=<port>   sets the port the dashboard is available on
  --save-max=<number>       changes the max number of entries saved locally
  --save-file=<path>        changes the filepath used for local db, json ext required
  --no-dashboard            disables the dashboard, --dashboard-port is ignored
  --no-save                 disables saving locally, --save-file + --save-max ignored
  --help                    what you're seeing right now :)
```

Some of the options above have defaults associated with them, they are:

- **port** default is `8080`
- **dashboard-port** default is `8081`
- **save-max** default is `50`
- **save-file** default is `~/.reqon/db.json`

## Viewing requests

Whenever a request is made to the listening endpoint, it's recorded and displayed immediately in the terminal. The details include the full route, http method, headers, url query variables, and request body.

If you'd prefer something a bit more... easy on the eyes, a dashboard server is also spun up by default. It can be visited to see all of your current and past saved requests, along with their details, in a simplistic but effective layout.

![A screenshot of the reqon dashboard](https://dev-to-uploads.s3.amazonaws.com/uploads/articles/iukiwlpekei8dcsiygui.png)

By default, requests are stored locally in a JSON file with the help of [LowDB](https://github.com/typicode/lowdb).

## Wrapping up

Thanks for checking this project out!

If you end up using it and have any suggestions or issues you'd like to discuss, please feel free to let me know. You can put in a request directly on the [GitHub repo](https://github.com/aschmelyun/reqon), or reach out to me on [Twitter](https://twitter.com/aschmelyun).