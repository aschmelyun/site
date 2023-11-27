---
title: 5 tips for supercharged Laravel Eloquent queries
slug: 5-tips-for-supercharged-laravel-eloquent-queries
description: Enhance and optimize your Laravel Eloquent queries with these helpful hints, saving time and cleaning up your code in the process.
categories: laravel
published_at: Jul 3 2020
excerpt: I've been working with Laravel for the last five years or so, and over that time I've come across a few cases where I needed a unique or atypical way of returning a piece of data from my application. Using Eloquent makes fetching data with Laravel easy, but there's still a few use cases where it took me some digging and understanding to figure out how to do what I was trying to accomplish.
---

I've been working with Laravel for the last five years or so, and over that time I've come across a few cases where I needed a unique or atypical way of returning a piece of data from my application. Using Eloquent makes fetching data with Laravel easy, but there's still a few use cases where it took me some digging and understanding to figure out how to do what I was trying to accomplish.

I've listed out five of these below, along with code examples and dummy data returned for each. This article does anticipate that you're at least a little familiar with Laravel and Eloquent models, and at the time of writing was aimed toward Laravel 7. Although these hints *should* work for any app using version 5+ of Laravel.

If you're more of a visual learner, I've [published a video on YouTube](https://www.youtube.com/watch?v=TuPdEbEBvo0) following along with these and the examples given.

*Ready?* **Let's get started!**

## Building a query conditionally

Let's say that we have a **Property** model with columns for the rent price and whether or not you're allowed to have pets. And we'd want to filter out data when the user visits our app at `example.com/properties?rent=1200&pets=true`.

Filtering out properties by returning from each conditional (or combination of them) could get complex fast, the more filters that you add into it:

```php
public function test(Request $request)
{
    if ($request->get('rent') && $request->get('pets')) {
        return Property::where('rent', <=, $request->get('rent'))->where('pets_allowed', true))->get();
    }

    if ($request->get('rent')) {
        return Property::where('rent', <=, $request->get('rent'))->get();
    }

    if ($request->get('pets')) {
        return Property::where('pets_allowed', true))->get();
    }

    return Property::all();
}
```

Instead, we can build up a query and then add onto it based on those conditionals (instead of relying on them). Using **Model::query()** to open up what's essentially an Eloquent query placeholder, we can then chain on **where()** statements based on what filters are present. A final **get()** call returns our data:

```php
public function test(Request $request)
{
    $properties = Property::query();

    if ($request->get('rent')) {
        $properties->where('rent', <=, $request->get('rent'));
    }

    if ($request->get('pets')) {
        $properties->where('pets_allowed', true));
    }

    return $properties->get();
}
```

This ensures that each filter only needs one conditional statement, and that combinations of filters are chained on to this query automatically.

## Returning the latest relationship

Using our previous **Property** model as an example, let's say that there's multiple **Tenant** models associated with each in a `hasMany()` relationship. To pull in all of the properties with their attached tenants, you'd probably use something like:

```php
public function test(Request $request)
{
    return Property::with('tenants')->get();
}
```

But what if you wanted to only return one tenant? Say, the one who's lease expires the furthest away from today. You could run a nested query on the `with` statement above, but if you tried to limit it by 1 it wouldn't return any values as expected.

Instead, in Laravel, you can create a `hasOne` relationship on the same class that a one-to-many exists on. Chaining any other conditions you want onto it, it'll just return a single attached model.

So, the relationships in our **Property** model now look something like:

```php
public function tenants()
{
    return $this->hasMany(Tenant::class);
}

public function newestTenant()
{
    return $this->hasOne(Tenant::class)->orderBy('lease_expires_at', 'desc');
}
```

Now, if we go back to our previous test method and modify it to use `Property::with('newestTenant')`, we'll get back just a single tenant, and the one whose lease expires at the date furthest from today.

## Filtering items by nested values

Using our **Property** and **Tenant** models and relationship from earlier, what if you wanted to only return those properties whose tenants don't have dogs or cats? You might be able to use something like this:

```php
public function test(Request $request)
{
    return Property::with(['tenants' => function($query) {
        $query->where('has_dogs', false)->where('has_cats', false);
    }])->get();
}
```

Which, will partially work. In our returned data, we'll only see tenants who don't have dogs or cats. But, the problem is that if a property has nothing but tenants with cats and dogs, we're left with an empty array of tenants on the property model.

What I'd like to do is filter these properties out and just return the ones that contain those filtered relationships. We could just run a foreach loop on the objects and check for that empty tenants array, or we could use Eloquent's `whereHas()` method:

```php
public function test(Request $request)
{
    return Property::whereHas('tenants', function($query) {
        $query->where('has_dogs', false)->where('has_cats', false);
    })->with(['tenants' => function($query) {
        $query->where('has_dogs', false)->where('has_cats', false);
    }])->get();
}
```

By using `whereHas()`, the above *only* returns those properties that match the column entered as the first argument. Our second argument filters that column based on chained methods to the query object. In this case, any tenants that don't have cats and dogs.

We then follow up by attaching those tenants to the returned properties and get the results.

## Generating and inserting dynamic attributes

For our fourth tip, let's say that we have two new models: **Technicians** and **Requests**. These are paired with each other in a many-to-many relationship, based off of a pivot table.

As such, a technician shares multiple requests with other technicians. What if we wanted a way to easily see, at a glance in our data, how many requests each technician object has? We could just include each of them as a lazy-load and then get the length of the array, or we could create a dynamic attribute to calculate and hold this value.

To get started, we'd have to add the following method to the end of our **Technician** model:

```php
public function getRequestsCountAttribute()
{
    return $this->requests()->count();
}
```

In Laravel, generated assets follow a particular naming convention for their methods:

- Use camelCase
- Start with `get`
- Contain the column name you want next
- End with `Attribute`

So, the method above will create a dynamic column on our returned technician called `requests_count`, and it will contain the count of the connected requests attached to our model.

## Filtering dates the easy way

For our fifth and final tip, let's go back to our **Tenant** model. As we mentioned previously, each one has a column to hold when their lease expires. Well, what if we wanted to only return those users whose lease is expiring in July of 2021?

We might be able to do something like:

```php
public function test(Request $request)
{
    return Tenant::where('lease_expires_at', 'LIKE', '2021-07-%')->get();
}
```

That will work perfectly, and only return back the tenants that we want. But I'm not super keen on using `LIKE` statements where we don't have to. They can get messy, and Laravel provides us with two better methods:

```php
public function test(Request $request)
{
    return Tenant::whereMonth('lease_expires_at', '07')->whereYear('lease_expires_at', '2021)->get();
}
```

Using `whereMonth` and `whereYear`, we can filter out only those models whose lease expires at the provided combined values, July of 2021. We can even replace those hard-coded values with `$request->get('month')` or `$request->get('year')` if we wanted to provide some dynamic filtering capabilities.

**That's all for now!**

These are five simple yet powerful methods and organizational tips that you can use to increase your productivity with Laravel's Eloquent ORM. Additionally, this might help get you started about thinking about how to optimize your queries and reduce your overall code clutter.

If you have any questions about this, or any other web development topics at all, please feel free to reach out to me on [Twitter](https://twitter.com/aschmelyun)!

If you would like a compiled list of web dev tips and tricks sent out to you on a regular basis, consider [signing up for my newsletter](https://aschmelyun.substack.com) which gets sent out every other week.