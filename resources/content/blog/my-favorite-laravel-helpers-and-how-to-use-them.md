---
view: layout.post
title: My favorite Laravel helpers and how to use them
description: I go through my top seven Laravel helper functions and show off a few use cases for each of them.
categories: laravel
published: Apr 12 2020
excerpt: As a full-stack PHP developer who works with Laravel on a day-to-day basis, I'm always looking for shortcuts and helpful methods in the framework that I can use to cut down on development time or code complexity.
---

As a full-stack PHP developer who works with Laravel on a day-to-day basis, I'm always looking for shortcuts and helpful methods in the framework that I can use to cut down on development time or code complexity.

I've compiled the following list of my current favorite helper methods that I use on a fairly regular basis. As of the writing of this article these are all available in Laravel 7, with some having been around a bit longer than that (so be sure to check against your app's version).

Let's dive in! ✨

### Str::limit()

Our first helper **takes a string and truncates it down to a set character limit**. There are two required parameters: the string that you want to truncate, as well as the character limit of the returned truncated string.

```php
use Illuminate\Support\Str;

$truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20);

// The quick brown fox ...
```

You can also pass in a third optional argument to control what is displayed after the string is returned.

```php
use Illuminate\Support\Str;

$truncated = Str::limit('The quick brown fox jumps over the lazy dog', 20, '[...]');

// The quick brown fox [...]
```

I've found this is perfect for shortening large blocks of text into something like an excerpt or post preview on an articles list.

### head()

This one is what makes this helpers fantastic. A simple method that's usually comprised of a couple nested vanilla PHP functions, head **returns back the first element of an array**. I've used it in a ton of different applications because of its global and ubiquitous nature.

```php
$array = [100, 200, 300];

$first = head($array);

// 100
```

Want the inverse of that? Use the `last` method to grab, well, the last element of an array.

```php
$array = [100, 200, 300];

$last = last($array);

// 300
```

The best part about using the head and last methods, they don't affect the original array at all.

### Str::between()

Another pretty self-explanatory method, the Str::between helper **returns back a string between two other strings**. If no string can be found with the start and end strings that you provided, `null` is returned.

```php
use Illuminate\Support\Str;

$slice = Str::between('My name is Inigo Montoya.', 'My name is ', '.');

// 'Inigo Montoya'
```

I love using this method to grab information between parentheses and brackets, return specific segments of URLs, or even parse out data between HTML tags.

### blank()

This helper is best explained as **empty, but better**. It returns a simple boolean true/false value depending on whether or not what was passed in contains any real data.

```php
// all of these return true
blank('');
blank('   ');
blank(null);
blank(collect());

// all of these return false
blank(true);
blank(false);
blank(0);
```

I've found the blank method particularly helpful in combination with request data validation, or weeding through API data that might not be formatted properly. It's a little nicer than using something like `empty(trim())`.

### Str::contains()

The helper method all of us have been waiting for, **determining if a string contains another string**. This question has been asked *so* many times on [StackOverflow](https://stackoverflow.com/questions/4366730/how-do-i-check-if-a-string-contains-a-specific-word) and other programming forums, with the current best practice in vanilla PHP using `strpos`.

```php
use Illuminate\Support\Str;

$contains = Str::contains('My name is Inigo Montoya.', 'Inigo');

// true

$contains = Str::contains('My name is Inigo Montoya.', 'Andrew');

// false
```

With the approval of a recent rfc, PHP will have its own str_contains method soon that will render this obsolete. Until then though, it's one of the most useful helper methods in Laravel.

### Arr::pluck()

Arguably one of the more powerful methods I've listed off in this article, Arr::pluck lets you **walk through a nested array and return an array of values based on their keys**.

Let's check out a brief example:

```php
use Illuminate\Support\Arr;

$array = [
    ['website' => ['id' => 1, 'url' => 'reddit.com']],
    ['website' => ['id' => 2, 'url' => 'twitter.com']],
    ['website' => ['id' => 3, 'url' => 'dev.to']],
];

$names = Arr::pluck($array, 'website.url');

// ['reddit.com', 'twitter.com', 'dev.to']
```

Passing in an array and a dot-notation string to determine what key values we want, the multi-dimensional array is walked and a single flat array of the key's values are returned back to us.

I've used this multiple times in returned API data (when I don't feel the need to use a full collection). It makes getting something like an array of IDs, names, or other attributes insanely easy without having to create a whole foreach loop.

### collect()

Once I found out about collections, I haven't stopped using them. This is probably the helper I find myself reaching for most often, and it lets you **transform an array into a [Collection](https://laravel.com/docs/7.x/collections)**.

Why is this important? Because collections are like arrays on steroids. They come with a massive [library of methods](https://laravel.com/docs/7.x/collections#available-methods) that you can chain together to perform all sorts of filtering, sorting, and modifying on an array with simple closure arguments. No foreach loops, no intermediary variables, just clean code.

Check out this quick example:

```php
$collection = collect(['Keys', 'Krates']);

return $collection->map(function ($value) {
    return Str::upper($value);
});

// ['KEYS', 'KRATES']

return $collection->filter(function ($value) {
    return strlen($value) > 4;
});

// ['Krates']
```

This is honestly just the tip of the iceberg with what collections can do. I've used them all over my projects, especially when I'm dealing with large and intricate data sets that aren't coming from my database models. CSV data, external API requests, and directory structures can all benefit from being put into a collection.

**That's all I have for now!**

If you have any suggestions for what should have been on this list, or I've made you discover your new favorite Laravel helper, I'd love to hear about it. Feel free to send me a message on [Twitter](https://twitter.com/aschmelyun), or leave a comment below!
