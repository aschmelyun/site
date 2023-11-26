---
view: layout.post
title: How to use Laravel’s bind and singleton methods
description: Up until this point I really hadn’t looked into or thought about the bind or singleton methods. I decided to do some digging and take time learning how, and when, to use those methods in my own applications.
categories: laravel
published: Nov 27 2019
excerpt: A while back, someone pointed out that in my Laravel package tutorial, my use of a singleton method was completely unnecessary. Truth be told, up until this point I really hadn’t looked into or thought about the bind or singleton methods that I’ve seen in the source code of other packages. I decided to do some digging and take time learning how, and when, to use those methods in my own applications.
---

A while back, someone pointed out that in my Laravel package tutorial, my use of a singleton method was completely unnecessary. Truth be told, up until this point I really hadn’t looked into or thought about the bind or singleton methods that I’ve seen in the source code of other packages. I decided to do some digging and take time learning how, and when, to use those methods in my own applications.

Feel like watching an explanation instead of reading? Check out [the video of this tutorial](https://www.youtube.com/watch?v=yg1qOom6YuE) instead.

If we take a look at the Laravel [documentation](https://laravel.com/docs/5.8/container), binds are registered using the `bind()` method. The first argument passed in is a class name, followed by a closure that returns an instantiated instance of that class object. What we’re telling Laravel is “**Keep this class handy, and whenever I reference it through the app service provider, I want to get back an initialized class object with any configurations or arguments that I pass in here**”.

```php
$this->app->bind('HelpSpot\API', function ($app) {
    return new HelpSpot\API($app->make('HttpClient'));
});
```

## Creating our example class

In a brand new Laravel project, let’s navigate to the web.php route file and remove the default view return. Instead of creating a controller, I’m just going to do these demonstrations right in the route’s closure.

![Our blank Laravel project’s web.php route file](https://miro.medium.com/max/2406/1*0Bd1naZ4P-67RWy2o8S1-A.png)

To start our little example, let’s create a class called ExternalApiHelper.php under a `Helpers` directory, in our `app` folder. We can assume that in a production environment, this class might be a way to expose multiple methods for interacting with a third-party service or some other external API.

We’ll add the proper namespace, class name, and initialize it with a single method called `foo()`, that returns a string with a value of **bar**.

![Our ExternalApiHelper class with a single foo() method](https://miro.medium.com/max/2406/1*qTo0XC8UhX-3C40i-gc4Rg.png)

If we navigate back to our web.php route file, we can call this class like any typical PHP method. First we initialize it with the **new** keyword, and then return the value of the method foo. Like this:

```php
use App\Helpers\ExternalApiHelper;

$apiHelper = new ExternalApiHelper();
return $apiHelper->foo();
```

Navigating to our project in the browser, we should see that ‘bar’ is returned just as expected.

Let’s add a bit more complexity to this ExternalApiHelper class. We’ll add in a private variable to store the value of foo, and set that value in a constructor. Instead of hard coding in the return value of the `foo()` method, we’ll return whatever is set in that variable.

![Added complexity to our class with a private variable and constructor](https://miro.medium.com/max/2400/1*1aOABCLsJqXODORA_l2SpA.png)

If we modify the line of code in the web.php route file to match the new class structure, we can change what the browser returns:

```php
$apiHelper = new ExternalApiHelper('Hello, world!');
```

Now, this definitely works and isn’t that complicated. But if you begin dealing with larger classes and more complex code structures, it can get pretty messy. My goal here is to get a very Laravel-like one liner, that might look something like:

```php
return ExternalApiHelper::foo('Hello, world!');
```

## The service provider

If we open up the `AppServiceProvider.php` file, we’re presented with two methods by default: `register()` and `boot()`. We’re going to be focusing on the first one, as any binds or registrations that need to be made to our service container, have to be set in here.

Binding is easily done by calling the bind method, through the app’s service container:

```php
$this->app->bind()
```

As we saw earlier, the first argument will be our class name (which for organization’s sake we’re pulling through with the static class keyword). Followed by that is our closure, which returns a new instance of our ExternalApiHelper class initialized with a string of our choosing:

```php
$this->app->bind(ExternalApiHelper::class, function() {
    return new ExternalApiHelper('Hello, app!');
});
```

Back on our web.php route file, we can replace the two lines of code in our closure with a single line now. The `app` method used is a short call to our application’s service provider, inside of which we’ll pass in the class name that we bound. Since this returns an initialized object of our class, we can then chain the foo method right to the end of it and return our string as expected:

![ExternalApiHelper called through the service provider](https://miro.medium.com/max/2406/1*q0-WqPOWwhx-pGcmdtGOpA.png)

A little earlier we talked about wanting to use a Laravel-like static one liner to call this method instead, and there’s a pretty simple way to accomplish this. Let’s head back to our ExternalApiHelper class and add in a new static method called `bar()`.

This method will just simply return the call that we’re making in the web.php route file:

```php
public static function bar()
{
    return app(ExternalApiHelper::class)->foo();
}
```

If we go back to our web route file and replace the line with `ExternalApiHelper::bar()`, save, and refresh our browser, we get back the same value that we have been (**Hello, app!**), only in a cleaner, shorter line.

Let’s ramp it up and add a bit more complexity to this. We’ll create another static method called setFoo in our ExternalApiHelper class, with a single parameter called `$foo`. We’ll get our class object by calling the app method again, but we’re going to set the private `$foo` variable using the argument passed in when this method is called. Afterwards, we’ll just return the whole object:

```php
public static function setFoo($foo)
{
    $apiHelper = app(ExternalApiHelper::class);
    $apiHelper->foo = $foo;
    return $apiHelper;
}
```

Back on our web.php route file, we can directly call that setFoo method without having to initialize the class first.

The service provider will do all of the work for us, then we pass in the string that we want, and call the foo method on that object. We could honestly even shorten this down and chain `foo()` directly on the end of the first line if we wanted to:

![ExternalApiHelper being called directly with the help of the service provider
](https://miro.medium.com/max/2410/1*KmedBZg-nEUqrlo3C1yEWQ.png)

## The main difference between bind and singleton

This is a perfect time to look at the difference between these two methods. Let’s remove the return on the code example above, and call a second `ExternalApiHelper::setFoo()` method, passing in a different string than the first.

We’ll try and return both of the object’s values, separated out by a dash:

```php
$externalApi = ExternalApiHelper::setFoo('Hello, foo!');
$externalApiAgain = ExternalApiHelper::setFoo('Hello, foo again!');

return $externalApi->foo() . ' - ' . $externalApiAgain->foo();
```

Refreshing our browser window, we can see the two values that we set and want displayed, “**Hello, foo! — Hello, foo again!**”. Each variable that we created has its own ExternalApiHelper class object, and is storing its own foo string.

If we navigate back to `AppServiceProvider.php` and change the **bind** call to a **singleton** call, keeping everything else the same, and refresh the browser, we see something a little different:

**“Hello, foo again! — Hello, foo again!”**

The reason behind this is the singleton pattern in PHP:

> The **singleton pattern** is used to restrict the instantiation of a class to a single object, which can be useful when only one object is required across the system. … The first call will instantiate the object while any subsequent will only return the instantiated object.

What does this mean for us? When we call `ExternalApiHelper::setFoo()` for the second time, instead of creating a new object and assigning it to our variable, it’s referencing the object that was created in the call before it. We we set a new string using that method, we’re setting the string on the same object, just stored in two separate variables.

Calling the `foo()` method on those objects returns the same value because the reference is to the same class object.

## Why would we use singleton?

A perfect use case for this pattern is in something like an application logger.

Let’s say that we have a class that gets data passed into it, and then sends that data out to a third-party service or stores it locally on the filesystem. Regardless of the intention, after the logger has been initialized for the first time, we wouldn’t want to (or need to) reference more than one instance of that class.

If during your code’s execution Logger is needed 5 different times, **not using the singleton pattern would result in calling 5 different instances of that Logger class object**. Unless you’re destroying them after each successful event, that just wastes hardware memory and can lead to messier code overall.

Using a singleton ensures that only one object of that class is initialized at a time, throughout our application’s execution.

Another great example would be for a database. We wouldn’t need to initialize a database class object with login credentials multiple times through our applications execution when we can just reference a single object that’ll handle our calls for us.

I hope you’ve learned as much from this as I did researching it and putting it together! Hopefully you’re confident enough to start using `bind()` and `singleton()` service provider methods in your own Laravel applications.

As always, if you have any questions please don’t hesitate to reach out to me on my [Twitter](https://twitter.com/aschmelyun)!