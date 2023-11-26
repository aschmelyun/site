---
title: Storing time-series data in InfluxDB with Laravel
slug: storing-time-series-data-in-influxdb-with-laravel
description: I needed to store time-series data with InfluxDB from a variety of IoT sensors, but wanted to use it alongside an existing Laravel app installed on a Raspberry Pi.
categories: laravel
published_at: Mar 19 2022
excerpt: I've been running a Laravel application on a Raspberry Pi that handles the bulk of my home automation and monitoring, like keeping track of temperatures and humidity from sensors around my house. Originally I was storing this all on a MySQL database, and yeah that worked good enough. But, after 2 years of continuous data it was starting to get a little bulky.
---

I've been running a [Laravel application on a Raspberry Pi](https://aschmelyun.com/blog/building-an-interactive-raspberry-pi-dashboard-with-laravel-grafana-and-docker/) that handles the bulk of my home automation and monitoring, like keeping track of temperature and humidity from sensors around my house. Originally I was storing this all on a MySQL database, and yeah that worked good enough. But, after 2 years of continuous data it was starting to get a little bulky.

I figured, if I'm storing time-series data, it makes sense to use a popular [open-source solution](https://github.com/influxdata/influxdb) that's optimized exactly for that. In this article, I'm going to show you how I tied in the **time-series database InfluxDB into my existing Laravel application**. After doing this, not only have my response times improved drastically, but the database size has shrunk considerably as well.

Let's get started!

## Quick Notes

In this article, I'll assume you have the following already set up and ready to go:

- A Laravel 8+ application
- An InfluxDB V2 database
- A bucket (database) created in InfluxDB for your data

I won't be going over _how_ to get an InfluxDB instance set up as there's a few different ways that this can be accomplished. If you'd like me to dive in deeper in a separate article about how I got it working (in this particular case using Docker and Docker Compose), feel free to let me know either in the comments or on Twitter!

## Getting Started

Alright, the first thing that we need to do is install the InfluxDB PHP client so that we can use it to connect to our database through our Laravel application. There's something important to note here though.

> **There are two completely different libraries depending on the version of InfluxDB that you're using.**

I'm using InfluxDB version 2 as stated in the prerequisites list above, so [this is the library](https://github.com/influxdata/influxdb-client-php) I'm going to install. There are pretty significant syntax differences, and if you follow this tutorial trying to connect to an older V1 instance, you'll probably run into problems down the line.

So, in our project root, we can install that dependency like this:

```bash
composer require influxdata/influxdb-client-php
```

Then in our Laravel application's `.env` file, we'll add in a few new values to handle connecting to the InfluxDB instance:

```text
INFLUXDB_HOST=
INFLUXDB_TOKEN=
INFLUXDB_BUCKET=
INFLUXDB_ORG=
```

- **INFLUXDB_HOST**: This is the hostname and port that your instance resides on. If you're self-hosting, it's likely that this will be `127.0.0.1:8086`
- **INFLUXDB_TOKEN**: The access token. This can be set on creation of the InfluxDB instance, or automatically generated. It's found in the InfluxDB dashboard under the *Data > API Tokens* section.
- **INFLUX_BUCKET**: A bucket in InfluxDB is basically a database, and this should correspond to the name of the one you created to store your data in.
- **INFLUXDB_ORG**: The organization name for your InfluxDB instance, usually specified during setup.

Once you have all of these added to your `.env` file, we can move on to adding in code to test our connection out. Let's create a route, controller, and method to handle an incoming data point that needs to be saved in our bucket.

In the `routes/web.php` file, it could look something like this:

```php
Route::post('/temperature', [\App\Http\Controllers\TemperatureController::class, 'store']);
```

And then that controller class, `TemperatureController.php`, would look like this:

```php
namespace App\Http\Controllers;

class TemperatureController extends Controller
{
    public function store(Request $request)
    {
        // store our data point in InfluxDB
    }
}
```

Alright, now we're ready to start writing code to actually use our database!

## Setting Up The Client

First, we need to instantiate a Client class from the InfluxDB PHP library that we installed earlier.

Because this is version 2, the main namespace is `IndexDB2`. The client constructor requires an array to be passed in, containing the data necessary to connect to the instance and determine the precision of the bucket. That looks like this:

```php
$client = new \IndexDB2\Client([
    'url' => env('INFLUXDB_HOST'),
    'token' => env('INFLUXDB_TOKEN'),
    'bucket' => env('INFLUXDB_BUCKET'),
    'org' => env('INFLUXDB_ORG'),
    'precision' => \InfluxDB2\Model\WritePrecision::S
]);
```

The first four attributes are pretty obvious, they're the values that we added into our `.env` file earlier, and we're using the `env()` helper to pull those values out. The last one however, is new.

*Precision* determines what the format and precision of the timestamps associated with data in your bucket will be. There's four possible values that we can use:

- **s**: Seconds
- **ms**: Milliseconds
- **us**: Microseconds
- **ns**: Nanoseconds

The `WritePrecision` class is merely a helper enum that returns back one of those four possible values. I'm writing data once every 2 minutes, so precision above seconds isn't *really* necessary to me.

## Adding Data

After the client has been initialized, we have to call a method on it to create something called a Write API:

```php
$writeApi = $client->createWriteApi();
```

This `writeApi` object exposes a few methods that we can use to **write data to our bucket**. 

Before we do that, we first have to create a **Point** object. Then using a chain of methods, set it up to be used by our Write API object.

```php
$point = new Point::measurement('temperature');
```

This sets up a measurement datapoint called `temperature`.

Let's say that I have a bucket called 'metrics', I can have multiple different attributes all inside of that one bucket (like temperature, humidity, light level, etc) and be able to query them individually or together as needed.

The Point object then needs to have a value associated with it, so we use `addField` to attach that:

```php
$point->addField('fahrenheit', 74.3);
```

If we have multiple measurements with the same name that are in the bucket, we can differentiate _those_ by using tags to determine things like room placements:

```php
$point->addTag('location', 'bedroom');
```

Multiple tags can be used, but I just need the one.

Finally, we need to set _when_ this measurement was taken by using the `time()` method. Remember that I set my write precision to seconds, so I can just use the amount of epoch seconds.

In PHP, we'll use the default `time()` function for that:

```php
$point->time(time());
```

Now that we have our point all configured, we just write it to our bucket using the Write API object we created earlier:

```php
$writeApi->write($point);
```

This will return back `true` if everything goes well, or throw an exception if there was something wrong along the way. After that, your data should now be available in your InfluxDB bucket!

## Wrapping Up

Refactoring the above code a bit and putting it all together, our `TemperatureController`'s entire store method looks like this:

```php
public function store(Request $request)
{
    $client = new \IndexDB2\Client([
        'url' => env('INFLUXDB_HOST'),
        'token' => env('INFLUXDB_TOKEN'),
        'bucket' => env('INFLUXDB_BUCKET'),
        'org' => env('INFLUXDB_ORG'),
        'precision' => \InfluxDB2\Model\WritePrecision::S
    ]);

    $writeApi = $client->createWriteApi();

    $point = Point::measurement('temperature')
            ->addTag('location', 'bedroom')
            ->addField('fahrenheit', 74.3)
            ->time(time());

    try {
        $result = $writeApi->write($point);
    } catch(\InfluxDB2\ApiException $e) {
        return $e;
    }

    return $result;
}
```

And after hitting that route endpoint with a request, we can go to our InfluxDB dashboard and see the datapoint that we added. In this case, a temperature reading of 74.3, tagged in bedroom. Now you're ready to easily ingest and query time-series data in your Laravel application with the help of InfluxDB!

If you have any questions about this, web development in general, or would like to see more shorter-form content from me, feel free to follow me on Twitter [@aschmelyun](https://twitter.com/aschmelyun).