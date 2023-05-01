---
view: layout.post
title: Using single action controllers in Laravel
description: Fleet adds a few helpful Artisan commands to manage Laravel Sail applications and binds them to custom domain names.
categories: laravel
published: Feb 4 2023
excerpt: This package exposes a few helpful Artisan commands to manage multiple Laravel Sail applications running concurrently on your local environment. All powered by a Traefik Docker container, letting you map custom domain names to your different Laravel applications and handling the incoming traffic to them.
---

> Want to watch a video on this topic instead? [Check it out here](https://www.youtube.com/watch?v=lLj8UYrqLuU) on my YouTube channel.

When working with Laravel applications, you've probably seen code like this in a routes/web.php file:

```php
Route::get('/posts', [PostController::class, 'index']);
Route::post('/posts/create', [PostController::class, 'store']);
Route::delete('/posts/{post}', [PostController::class, 'destroy']);
```

As a route is requested, the event is piped through a controller (in the above case, PostController) and to a specific method in that controller given as the second array attribute.

This works well for slim resource methods, but depending on your application's complexity, business logic can start adding up in your controller classes and end up making them pretty long. There's a few popular ways to refactor that, like adding logic inside the model, creating repositories, or by using single-use invokable controllers.

Let's see what that looks like.

First, opening up the terminal we can create one of these controllers with a handy Artisan command:

```bash
php artisan make:controller ShowAllPostsController --invokable
```

That last argument (invokable) means that we want Laravel to scaffold out a controller for single use. If we open it up, we see a typical controller, but it comes with a single method:

```php
__invoke(Request $request)
{
    //
}
```

This is actually a built-in [PHP magic method](https://www.php.net/manual/en/language.oop5.magic.php#object.invoke), and is normally called whenever a class is used like a function. Laravel uses this behind the scenes during the route resolution to call just this specific method inside of a controller class.

Speaking of route, let's go ahead and modify our previous routes to use this class:

```php
Route::get('/posts', ShowAllPostsController::class);
```

A bit shorter and cleaner syntax, since we don't have to use an array to define both the class _and_ the method that the route will use. It's just a single class, with a singular method that will be invoked when that endpoint is visited.

We can continue this futher and convert all of the routes before to single use controllers:

```php
Route::get('/posts', ShowAllPostsController::class);
Route::post('/posts/create', CreatePostController::class);
Route::delete('/posts/{post}', DeletePostController::class);
```

For any routes that contain parameters or route-model binds (like the delete one above), those can be accessed in the __invoke method just like a normal controller class' method:

```php
__invoke(Request $request, Post $post) {}
```

Alright, now each route endpoint above has been converted to single-use invokable controllers. It's a bit overkill for something like this, but you might be asking "Why would I want to use something like this?".

That answer, like most architectural and application design choices, is kind of up to you or your team. If you find it easier to organize you entire app by single-use actions, that might make sense to you. Maybe you just have a few actions that use a lot of heavy business logic, and so you reserve invokable controllers for just those particular ones. And of course, maybe this isn't for you at all, and that's okay!

Well, that's the gist of using single action controllers in Laravel! If you have any questions or comments about using them, please feel free to let me know by messaging me on [Twitter](https://twitter.com/aschmelyun).