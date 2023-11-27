---
title: Adding real-time updates to your Laravel and Vue apps with laravel-websockets
slug: adding-real-time-updates-to-your-laravel-and-vue-apps-with-laravel-websockets
description: With the help of the laravel-websockets package, it’s super easy to get a websockets server started in your Laravel application.
categories: laravel, vue
published_at: Jul 21 2019
excerpt: Earlier this month I launched listpal.co, a to-do app that included websockets functionality so that each user with the list open would see updates from everyone else. It was definitely a learning experience and my first time really diving into the world of Vue + websockets combined. With the help of the laravel-websockets package, it’s super easy to get a websockets server started in a new (or existing) Laravel application.
---

Earlier this month I launched [listpal.co](https://listpal.co), a to-do app that included websockets functionality so that each user with the list open would see updates from everyone else. It was definitely a learning experience and my first time really diving into the world of Vue + websockets combined. With the help of the [laravel-websockets](https://github.com/beyondcode/laravel-websockets) package, it’s super easy to get a websockets server started in a new (or existing) Laravel application.

**Prefer poking through code instead of reading articles?** The entirety of listpal.co is open source and hosted on my GitHub if you’d like to pore over the inner workings. Otherwise, let’s get started:

## Before we take off

Below I’m going to go through the steps to configure and implement the laravel-websockets package into an existing Laravel + Vue application. The following assumes you have a basic to-do app set up with a structure similar to this:

![Code screenshot of an example Laravel app file structure](https://miro.medium.com/max/1400/1*MbvnTf_96rwxrO12DT1MmQ.png)

Right now we currently have a single model (Item.php), a controller for it, a Blade template to bring in our Vue app which lists all of the items, and some basic routes in web.php. It’s assumed that on load, the Vue app calls `/api/items` to populate the list app, with a method involved to add new items in the list via `axios.post()`.

## Setting up an Event

[Events](https://laravel.com/docs/5.8/events) are Laravel’s way of decoupling code that fires whenever something in particular on your app happens.

> Laravel’s events provide a simple observer implementation, allowing you to subscribe and listen for various events that occur in your application. Event classes are typically stored in the `app/Events` directory, while their listeners are stored in `app/Listeners`.

For instance, if you’re running an ecommerce site you might have an event titled **ProductOrdered** which would send out an email confirmation whenever a customer purchased from your store. Instead of having that code tangled up in the product controller, it can be contained to this particular event class and fired automatically or with the global `event()` method.

We’re going to make an event for when an item is added to our list, so in your console at the root of your app run:

`php artisan make:event ItemAdded`

This will create the file **ItemAdded.php** under app/Events, leave it exactly how it is for now. For this demo we’ll be triggering the event manually, so in your ItemController’s `store()` method add the following line. I’d recommend putting this right before your return statement:

`event(new App\Events\ItemAdded());`

There’s a little more to this event that we’ll dive into later, but now it’s time to set up our websocket server.

## Adding and configuring laravel-websockets

The [laravel-websockets](https://github.com/beyondcode/laravel-websockets) package by Marcel Pociot and Freek Van der Herten has been nothing short of incredible. What used to take a separate Node server running laravel-echo-server or socket.io can now be done entirely with PHP (and in the case of this example, within the same Laravel app codebase 🤯).

Aside from that, the biggest pro for using this is that it’s a direct replacement for Pusher and **fully integrated with Laravel out-of-the-box**. Only the smallest amount of configuration is required to get the server up and running, and your app will happily start sending out broadcasts.

Running the following commands from your app’s root will install the package, set up the necessary migrations, and publish a config file:

![Code screenshot of the steps to install laravel-websockets](https://miro.medium.com/max/1400/1*H7_ARO70qiTKNUlq24c1Yg.png)

Feel free to open up `config/websockets.php` if you want to take a look at it, however there’s *really* nothing we really need to do here. The only thing we have to do is set an ID, key, and secret for the package to use. Luckily it pulls from the PUSHER_ values in our .env file. You can set these to anything you’d like, but I’d keep them a little relevant to the project:

```
PUSHER_APP_ID=todoappid
PUSHER_APP_KEY=todoapp
PUSHER_APP_SECRET=todoappsecret
```

Save the file, navigate to the project root in your terminal, and run
`php artisan websockets:serve` ✨. Our websockets server is now live and awaiting instruction!

## Back to our event

Now that we have our websockets port up and running, we need to give it data to broadcast out. Again, Laravel has made this *insanely* easy. If you open up our file at `app/Events/ItemAdded.php` you’ll see the default class includes `Illuminate\Contracts\Broadcasting\ShouldBroadcast`. This interface can be implemented into our class enabling its use with Pusher (and thus laravel-websockets).

Any public variables that are set in this class are broadcasted out when this event fires. Since each time an item is added, we’d like our application to update its list with all items from the database, we’ll set a single public variable called `$items` and populate it in the constructor.

Finally, the `broadcastOn()` method will return a channel name of our choosing that this data will be broadcasted to. Everything tied together should look something like this:

![Code screenshot of an example Event in a Laravel app](https://miro.medium.com/max/1400/1*hs5n0FujLU6er--06IeCkQ.png)

That’s the end of the modifications needed to the backend of our app! Now, let’s move onto some JavaScript.

## Modifying the Vue front-end

If you’re using Laravel’s setup for Vue, chances are there’s a section commented out at the bottom of your `bootstrap.js` file that looks like this:

![Code screenshot of Laravel Echo in a JavaScript file](https://miro.medium.com/max/1400/1*uTaHz8vsEjjiETB46Br2bg.png)

Uncomment that entire block. This opens up our app to the Laravel Echo package, which will initialize at the beginning of our application. Using the key we’ve entered in our `.env` file, it’ll initialize the information we need to start listening to our websocket server.

The next step might be a bit tricky depending on how you’re pulling in data and whether or not you’re using a state management library like Vuex. **Let’s assume that you’re not**, and instead on the main Vue component you’re using an array in the data object to store the items, and calling a method using axios to post data to your API when an item is added. During the success handle you’re simply replacing the data in your component with what’s returned from the server.

However now, we’re going to implement Echo into a lifecycle method and have it listen on our channel we specified earlier. **When a callback is fired, we replace the data in the component with the public property that was broadcasted out through the event.** An example of how that would be set up could look like this:

![Code screenshot of an example Vue component with laravel-websockets](https://miro.medium.com/max/1400/1*gSfbqiLqoWMyQIgZKp4gEQ.png)

🎉 🎉 🎉

This removes the need to have our axios call touch our data, as soon as our new item is added to the server an event is fired and Echo handles it.

## Tying it all together

Just to reiterate, we’ve:

- Installed the laravel-websockets package using the default configuration

- Started the websocket server with `php artisan websockets:serve`

- Added an Event called **ItemAdded** that implements ShouldBroadcast

- Added Echo to our Laravel application’s bootstrap.js file

- Replaced the method of updating data in our Vue component with an Echo listener and removing the axios success callback

Personally, this has really opened up a world of opportunity for my projects. I always thought that any kind of websockets functionality would be a pretty large undertaking and require additional frameworks or separate server instances, but this method really proved me wrong.

Using this as a baseline you can expand upon it and add in different events for each model or action (save, delete, update), or use Echo in combination with Vuex to perform commits on your store’s data. The possibilities are pretty wide when you’re able to do this with PHP or inside of your existing Laravel app.

If you’d like to connect, have any questions, or would be interested in more bite-sized hints from the PHP/Laravel/Vue/general web-dev world, feel free to follow me on [Twitter](https://twitter.com/aschmelyun)!