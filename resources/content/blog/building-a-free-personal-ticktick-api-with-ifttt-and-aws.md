---
view: layout.post
title: Building a free TickTick API with IFTTT and AWS
cover_image: https://dev-to-uploads.s3.amazonaws.com/i/6jv4qpwf2ml5z1ooo3a7.png
description: TickTick doesn't currently have an open developer API, but with IFTTT integration and a few free Amazon AWS services, you can make a simple one that works well enough
categories: aws
published: Jan 22 2021
excerpt: This article doesn't expect you to have any in-depth familiarity with AWS or IFTTT. However, it is required that you have accounts for both, and recommended that you've at least played around with both of them a little bit. The free tiers of both of these services is all that's required to get your personal API running, so this doesn't cost a single cent.
---

This article doesn't expect you to have any in-depth familiarity with AWS or IFTTT. However, it is required that you have accounts for both, and recommended that you've at least played around with both of them a little bit. The free tiers of both of these services is all that's required to get your personal API running, so this doesn't cost a single cent.

**Disclaimer:** This is not intended to replace a full-featured API and only works for your own TickTick account. It was built for a very simple purpose of mine, but depending on your use case, it could be perfect for you as well.

Anyway, let's get started! 🎉

## Backstory

Over the past couple of months I've been experimenting with a little hardware project, the goal being to **display a current to-do item I'm focusing** on through an e-ink screen using a Raspberry Pi. On the program's startup, I was calling an API to grab my to-do list and store the items in an array, then cycling through each to display on my screen.

A problem came up when after the New Year I switched services to **[TickTick](https://ticktick.com)**. I had been thinking about moving away from the last app that I used, and the fact that they have a built-in Pomodoro timer solidified the switch for me and I haven't looked back.

**But, they don't have an open API.**

It seems like it's an item that has been [requested a few times](https://lmgtfy.app/?q=ticktick+api), and currently seems to be an item that they're working on. But as of the writing of this article, they do not have one available to the public. I get it though, pushing new features to production takes time, but that was time that I didn't want to wait. **So, I started digging around trying to find my own way.**

I thought I'd just go the brute force method and use something like [Puppeteer](https://pptr.dev/) to sign in to the app and scrape out any data I could. But then I remembered [IFTTT](https://ifttt.com) exists, and decided to see if they happened to have a trigger for tasks from TickTick. Lo and behold, they did! So my plan started coming together.

**Here's how I did it, and how you can too.**

## Getting Started

First thing's first, if you haven't already, sign up for an [Amazon AWS](https://aws.amazon.com/) account. This is what will end up powering our actual API, using the following services:

- **Lambda** for processing incoming data from our API endpoints and sending it to/getting it from our database
- **API Gateway** for creating our endpoints and connecting them to our Lambda instances
- **DynamoDB** for being the NoSQL database storing our todo items

All of these services have generous free usage tiers that don't expire. Unless you're going to be using this API every minute, **you shouldn't incur any direct costs.**

Everything we need to do in order to set up these three services is going to be handled through the command line using the  `serverless` app. We'll be loosely following [this tutorial](https://www.serverless.com/examples/aws-node-rest-api-with-dynamodb) to create our serverless AWS API.

Go ahead and open up a terminal or command prompt window, and run the following command to get the serverless app installed:

```bash
npm install serverless -g
```

> **Side Note:** Don't have Node or npm installed? Check out [this link](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm) and finish that before continuing with the above command.

Once that's completed, run this command to get the serverless todo demo API installed locally. This is going to be the basis for our API:

```bash
serverless install -u https://github.com/serverless/examples/tree/master/aws-node-rest-api-with-dynamodb -n ticktick-api
```

Navigate to the new folder created, `ticktick-api`, and open up the code with your favorite editor.

**Now it's time to start working on our API.**

## Creating our Serverless API

After opening up your new project, you should see a file structure that looks like this:

```
ticktick-api/
├── todos/
│   ├── create.js
│   ├── delete.js
│   ├── get.js
│   ├── list.js
│   └── update.js
├── package.json
├── README.md
└── serverless.yml
```

It's a pretty simple layout, each one of the files under the `todos` directory is a specific endpoint for our API. They're going to be powered by a **Node Lambda** instance, interact with our database, and return something back in the request. The `serverless.yml` file ties everything together, and generates the AWS services that we need in this API.

Before we actually deploy this though, we'll need to make some slight adjustments based on the data that we're pulling in and storing.

Opening up `create.js`, you'll see the following lines of code:

```js
if (typeof data.text !== 'string') {
  console.error('Validation Failed');
  callback(null, {
    statusCode: 400,
    headers: { 'Content-Type': 'text/plain' },
    body: 'Couldn\'t create the todo item.',
  });
  return;
}

const params = {
  TableName: process.env.DYNAMODB_TABLE,
  Item: {
    id: uuid.v1(),
    text: data.text,
    checked: false,
    createdAt: timestamp,
    updatedAt: timestamp,
  },
};
```

This isn't bad, but I'd like to add the ability to store the **list** from TickTick as well as the todo item's text. So, let's make some adjustments to add that datapoint in there as well:

```js
if (typeof data.text !== 'string' || typeof data.list !== 'string') {
  console.error('Validation Failed');
  callback(null, {
    statusCode: 400,
    headers: { 'Content-Type': 'text/plain' },
    body: 'Couldn\'t create the todo item.',
  });
  return;
}

const params = {
  TableName: process.env.DYNAMODB_TABLE,
  Item: {
    id: uuid.v1(),
    text: data.text,
    list: data.list,
    checked: false,
    createdAt: timestamp,
    updatedAt: timestamp,
  },
};
```

Okay, that's good. Let's open up our `update.js` file as well and add that **list** attribute there too. I won't show the before here, but this is what lines 12-37 should look like with both the text and list attributes:

```js
if (typeof data.text !== 'string' || typeof data.list !== 'string' || typeof data.checked !== 'boolean') {
  console.error('Validation Failed');
  callback(null, {
    statusCode: 400,
    headers: { 'Content-Type': 'text/plain' },
    body: 'Couldn\'t update the todo item.',
  });
  return;
}

const params = {
  TableName: process.env.DYNAMODB_TABLE,
  Key: {
    id: event.pathParameters.id,
  },
  ExpressionAttributeNames: {
    '#todo_text': 'text',
    '#todo_list': 'list',
  },
  ExpressionAttributeValues: {
    ':text': data.text,
    ':list': data.list,
    ':checked': data.checked,
    ':updatedAt': timestamp,
  },
  UpdateExpression: 'SET #todo_text = :text, #todo_list = :list, checked = :checked, updatedAt = :updatedAt',
  ReturnValues: 'ALL_NEW',
};
```

Lastly, open up `serverless.yml` and remove the `frameworkVersion: ">=1.1.0 <2.0.0"` line entirely. Save that file, and we're ready to deploy.

Back in the command line, run the following commands to install any dependencies, bundle everything together, and deploy your API:

```bash
npm install

serverless deploy
```

You should get back a set of endpoints like this:

```bash
Service Information
service: ticktick-api
stage: dev
region: us-east-1
api keys:
  None
endpoints:
  POST - https://xxxxx.execute-api.us-east-1.amazonaws.com/dev/todos
  GET - https://xxxxx.execute-api.us-east-1.amazonaws.com/dev/todos
  GET - https://xxxxx.execute-api.us-east-1.amazonaws.com/dev/todos/{id}
  PUT - https://xxxxx.execute-api.us-east-1.amazonaws.com/dev/todos/{id}
  DELETE - https://xxxxx.execute-api.us-east-1.amazonaws.com/dev/todos/{id}
functions:
  ticktick-api-dev-update: arn:aws:lambda:us-east-1:12345:function:ticktick-api-dev-update
  ticktick-api-dev-get: arn:aws:lambda:us-east-1:12345:function:ticktick-api-dev-get
  ticktick-api-dev-list: arn:aws:lambda:us-east-1:12345:function:ticktick-api-dev-list
  ticktick-api-dev-create: arn:aws:lambda:us-east-1:12345:function:ticktick-api-dev-create
  ticktick-api-dev-delete: arn:aws:lambda:us-east-1:12345:function:ticktick-api-dev-delete
```

>**Note:** If you get an error back about AWS provider credentials not being found, follow [this guide](https://www.serverless.com/framework/docs/providers/aws/guide/credentials/) to set them up in order to work with your serverless CLI app.

Alright, so now you should be able to visit your API endpoint at `https://xxxxx.execute-api.us-east-1.amazonaws.com/dev/todos`, and as of right now it should be returning back an empty array.

**If so, we're on the right track, and we can move on to getting data from TickTick into it.**

## Setting up IFTTT

Okay, let's head over to IFTTT and either create or sign in to your account. This is going to function as the tie between TickTick and our newly-created API.

Create a new Applet, and for the service select TickTick. You're presented with two possible triggers, of which we're going to use both. But for this first one, select **New task created**.

![Screenshot of IFTTT triggers for the TickTick service. Options are New task created, or New completed task](https://dev-to-uploads.s3.amazonaws.com/i/ykbakqmqeasapb0r15lw.png)

On the next screen, you'll determine if you want to limit this by only firing on select task lists, tags, or priorities. Me personally, I'm using the Inbox list, with all tags and any priority, so it looks like this:

![Screenshot of the options for a TickTick trigger in IFTTT. Options shown are List, with Inbox selected, Tag, with no selection, and Priority, with no selection.](https://dev-to-uploads.s3.amazonaws.com/i/b9fwrqfvl4tmm1mvxxtq.png)

Hit create, and we're brought back to the Applet creation screen for the **TT** in IFTTT.

Select the Webhooks service, and on the following page choose the only action available, **Make a web request**.

On this page, we're going to add in all of the details for the web request. The URL is going to be one of the ones returned to us by serverless earlier, and should end in just `/dev/todos`.

Set the method to `POST`, and the Content Type to `application/json`.

Finally, for the body, we're going to create a JSON string that will also contain data from our TickTick to-do item, pulled in automatically with the help of IFTTT.

All of these form fields should look like the screenshot below:

![Screenshot of a Web request action in IFTTT. The options are URL which says https://xxxxx.execute-api.us-east-1.amazonaws.com, Method which has POST selected, Content Type which has application/json selected, and Body which has a JSON string containing text TaskName and list List.](https://dev-to-uploads.s3.amazonaws.com/i/bz2sqpp6gzliareukdq5.png)

**Once that's saved, we're ready to test it out!**

Head over to your TickTick app, and create a new to-do item. When making the trigger in IFTTT, if you set any of the optional requirements like a specific list or tag, be sure to set those to make sure everything is working correctly.

Within a minute or so, we should be able to load up the main `/dev/todos` API endpoint in a browser and get back an array containing any of the items that we added to TickTick in our test. They each should contain the task name, list name, timestamps, and a unique ID. That ID can then be used to pull in a single item, update it, or delete it from the database altogether.

## Marking an item complete

You may have noticed that the above only pulls in items to our API when they're *created*. But, what if we want to update an item in our API when it's checked off in TickTick?

We can do that! First head back to the IFTTT dashboard and create a new Applet, with TickTick as the service. This time, choose the other trigger, **New completed task**. Again, choose what filters you would like (lists, tags, etc), and moving onto the action step, select Webhooks.

We're going to make a request to an endpoint on our API called `/dev/todos/complete`. Don't worry, we'll create this shortly. Use the `PUT` Method, `application/json` Content Type, the same Body from the create Applet, and save it.

Now, let's open our ticktick-api code back up, and take a look at the `update.js` file. This normally would expect an ID in the URL along with data attributes posted to it, in order to update a todo item. However, we're going to do some slight modifications in order to determine what item from what list needs to be updated, and mark it of as checked in our DynamoDB instance.

This is what that updated file should look like:

```js
'use strict';

const AWS = require('aws-sdk');

const dynamoDb = new AWS.DynamoDB.DocumentClient();

module.exports.update = (event, context, callback) => {
  const timestamp = new Date().getTime();
  const data = JSON.parse(event.body);

  const params = {
    TableName: process.env.DYNAMODB_TABLE,
    ExpressionAttributeValues: {
      ':text': 'text',
      ':list': 'list',
    },
    FilterExpression: 'list = :list and text = :text'
  };

  dynamoDb.scan(params, function (error, result) {

    result.Items.forEach(function (element, index, array) {
      const updateParams = {
        TableName: process.env.DYNAMODB_TABLE,
        Key: {
          id: event.pathParameters.id,
        },
        ExpressionAttributeNames: {
          '#todo_text': 'text',
          '#todo_list': 'list',
        },
        ExpressionAttributeValues: {
          ':text': data.text,
          ':list': data.list,
          ':checked': true,
          ':updatedAt': timestamp,
        },
        UpdateExpression: 'SET #todo_text = :text, #todo_list = :list, checked = :checked, updatedAt = :updatedAt',
        ReturnValues: 'ALL_NEW',
      }

      dynamoDb.update(updateParams, (error, result) => {
        const response = {
          statusCode: 200,
          body: JSON.stringify(result.Attributes),
        };
        callback(null, response);
      });
    });
  });
};
```

Now all we have to do is run `serverless deploy` from our terminal again to update our Lambda instances and we're good to go! If we check off an item in TickTick, that same item in our API will have a `checked: true` attribute.

## That's it!

You now have your very own personal TickTick REST API that you can use to store items you've added in your lists and reference them programmatically in other applications. If you have any questions about this, please feel free to let me know in the comments.

Follow me here or on [Twitter](https://twitter.com/aschmelyun) for more updates on what I'm using this API for, and other web development projects I'm working on.
