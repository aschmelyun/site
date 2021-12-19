---
view: layout.post
title: Getting started with Amazon S3 storage in Laravel
description: I'll show you how to set up an Amazon S3 bucket to store images and files with your Laravel app using a few built-in methods.
categories: laravel, aws
published: Mar 12 2020
excerpt: I've worked in the past on a few projects that use Amazon's S3 service to store images and files from Laravel applications. Even though the functionality is pretty much built into the framework, the process of getting started can be a little jarring, especially to those who don't have a whole lot of experience with the AWS suite.
---

I've worked in the past on a few projects that use Amazon's S3 service to store images and files from Laravel applications. Even though the functionality is pretty much built into the framework, the process of getting started can be a little jarring, especially to those who don't have a whole lot of experience with the AWS suite.

The benefits of using S3 can be pretty huge however, so I thought it was worthwhile to throw together this brief tutorial on how to get started tying your new (or existing) Laravel application's storage to an Amazon S3 bucket.

**Don't want to continue reading? Watch the video on it instead!**
{% youtube BQ0gi9YHuek %}

## Creating our project

To showcase the storage functionality, I'm going to build a super barebones image uploader in Laravel.

First thing's first, we're going to need three routes for this. Open up your `routes/web.php` file and create two GET requests, and a POST. These will be for the initial landing page, storing an image, and displaying a single image that was uploaded. These three will all use the same controller, `ImageController.php`, for the sake of simplicity.

Here's what I have for that:

```php
Route::get('/', 'ImageController@create');
Route::post('/', 'ImageController@store');
Route::get('/{image}', 'ImageController@show');
```

Then in our console at the project root, we can create that controller using artisan. Additionally, we can also generate the model with its migration using make:model with the `--migration` flag. Let's see how that looks.

```bash
php artisan make:controller ImageController
php artisan make:model Image --migration
```

For this demonstration app, we don't exactly need a ton of columns in our database table for the images. I think a filename and a url should suit that purpose just fine.

Opening up the new migration in the `database/migrations/` directory, let's modify it so that it looks like the following:

```php
public function up()
{
    Schema::create('images', function(Blueprint $table) {
        $table->bigIncrements('id');
        $table->string('filename');
        $table->string('url');
        $table->timestamps();
    });
}
```

If we look back at our `routes/web.php` file, we can see that we're going to need **three** methods in our ImageController. create(), store(), and update().

Create is an easy one, we literally just want to return a view that displays an image upload form so that we can add in an image and click a button to submit a form. Store needs a request parameter though, so that we can pull out the image data after that form has been submitted, and store it on our S3 bucket. Finally, update can have an Image parameter so that we can type-hint the return and stream the stored image directly to our user's browser.

Let's start with our form. Using TailwindCSS and a `resources/views/images/create.blade.php` file, I've made probably the most basic upload form I could think of.

![Screenshot of a super simple upload form](https://dev-to-uploads.s3.amazonaws.com/i/t83sl90a7xjten1fbart.png)

The markup for this is equally simple, it's a form that posts to the root page, where we've created our route that sends data to the `ImageController@store` method.

```html
<div class="max-w-sm mx-auto py-8">
    <form action="/" method="post" enctype="multipart/form-data">
        <input type="file" name="image" id="image">
        <button type="submit">Upload</button>
    </form>
</div>
```

## Saving an image locally

As with most everything else, Laravel makes it insanely easy to grab our file after it's uploaded and store it locally. In the `store()` method of our ImageController, we can call the file method on the $request object, passing through the name of our file input (`image`).

Chaining to that we can use the store method and specify a local path, that will automatically save the image file (with a randomly generated name and correct extension) to our local disk.

It's all wrapped up in a super simple, single line of code:

```php
$path = $request->file('image')->store('images');
```

Let's return that path back out to the browser for now.

If we then go back to our form in our web browser, select an image to upload, and click the 'Upload' button, we're presented with a relative file path to the stored image.

Going through to our Laravel app's `storage/app` directory, we can see that a new `/images` directory was created, and our image resides inside of it.

That's great! It works locally! Now it's time to migrate this functionality to Amazon. As I mentioned earlier, Laravel has most of this taken care of out-of-the-box. The only thing that we need to get this tied in is 4 different values in our application's `.env` file:

- AWS_ACCESS_KEY_ID
- AWS_SECRET_ACCESS_KEY
- AWS_DEFAULT_REGION
- AWS_BUCKET

Let's see how we can get those.

## Setting up an S3 bucket

Head on over to [aws.amazon.com](https://aws.amazon.com) and create an account (or sign in with your existing one). After you're in, take a look at the top menu bar and find the 'Services' item. If you click on that, you open up a box with Amazon's massive list of AWS services. Scroll down, and under the Storage section, select 'S3'.

On the following screen, you'll see a list of any S3 buckets that you've created, along with a blue "Create bucket" button. Click that! On the following pages, enter in your bucket name (which has to be unique across the entire AWS platform), and select the region most applicable for your bucket.

The rest of the pages should remain with the default values, and continue clicking the next button until your bucket is successfully created.

Alright, we have our bucket, but now we need credentials in order to access it programmatically. Clicking the 'Services' menu item again, search for **IAM**. This stands for **I**dentity and **A**ccess **M**anagement, and it's where we're going to create id/secret pairs for our newly-created bucket.

On the left-hand side of this screen, click the 'Users' item under the Access management group. On the following page, click the blue 'Add user' button.

Fill out a user name for your user, and check the box next to **Programmatic access**, this let's AWS know that we want to generate a key ID and secret access key for this user.

![Screenshot of AWS IAM user details page](https://dev-to-uploads.s3.amazonaws.com/i/qcngvvaxsevytve5ovkw.png)

The next page will probably be the most confusing part of this tutorial, and honestly it's pretty straight forward. Amazon let's you determine permissions on a per-user basis, and users can also be attached to groups if you have large amounts of them to manage.

For our simple demo (and honestly for most of my projects), I prefer going to the "Attach existing policies directly" section, searching for `S3`, and checking the box next to **AmazonS3FullAccess**. This ensures that our user (with the ID/secret attached), has full read/write access to our S3 bucket.

Click through the next few screens, leaving everything unchanged, and your user will be created successfully!

You'll be on a screen that contains your user created, along with its Access key ID and Secret access key. Copy these two values into your application's `.env` file under the appropriate headings listed above.

The other two items we'll need in our `.env` file we can pull straight from our bucket. The name that you used when you created it, and the region that you chose during the same step.

Now, we just have to tell Laravel to use S3 instead of our local disk.

## Connecting S3 to our application

Back in the `store()` method of our ImageController, all we have to do is make a single change to the one-liner that stores our files. In the `store()` method after 'images', add a comma and the string 's3':

```php
$path = $request->file('image')->store('images', 's3');
```

This tells Laravel that you want to use the S3 disk service, provisioned already in the services config of our app.

The final piece of this connection, is installing the package that Laravel uses as the bridge between our app and our S3 bucket. You can do that with the following line from your application's root:

```bash
composer require league/flysystem-aws-s3-v3
```

Okay, now let's go back to our application, and try uploading a file.

It works! A path is returned, but if we look at our `storage/app/images` directory, there's nothing new. That's because it was sent to our S3 bucket. If we refresh our bucket, there's now a folder called images, and clicking into it, we see our image that we uploaded!

Let's put those models we created earlier to use.

## Saving the image to the database

Back in our `store()` method in our ImageController, let's create a new image object after we store our image. Remember, we need just two values, a filename and a url. The filename we can get with the `basename` PHP method, and the url we can retrieve through the Storage facade's URL helper. Passing through our image's saved path, it conveniently returns back the full URL to our Amazon S3 image object.

This is what that model object creation looks like:

```php
$image = Image::create([
    'filename' => basename($path),
    'url' => Storage::disk('s3')->url($path)
]);
```

Now instead of returning the $path like we were previously, let's return the whole $image object.

Let's go back to our app's upload form, pick an image, and hit Upload. This time, we're given back some JSON that contains our image model's ID, filename, and URL.

![Screenshot of JSON image object from our Laravel app](https://dev-to-uploads.s3.amazonaws.com/i/wcwanhogvqd8tnaxonc4.png)

That image URL is also under a private lockdown right now, by default. If you click it, AWS returns an Access Denied error, and you're unable to view the image directly. Instead, we'll have to go about it a different way.

Back on our ImageController, we have a `show()` method, taking in our Image ID. We can use the type-hinted Image object, and thanks to the Storage facade again, we can both retrieve the image from S3 and stream it as a browser response with the appropriate content type. All of that with a single line of code:

```php
return Storage::disk('s3')->response('images/' . $image->filename);
```

If we go to a path on our app with the Image ID that was just returned to us, Laravel retrieves the image from our S3 bucket, and displays it directly in the browser.

## That's all

That's about it for now!

You've successfully learned how to:

- Upload image files and store them locally
- Set up an Amazon S3 bucket and assign credentials
- Convert local disk storage to use an Amazon S3 bucket
- Retrieve images from an S3 bucket with Laravel


If you'd like to learn more about Laravel development, Amazon AWS, or other general web dev topics, feel free to follow me on my [YouTube channel](https://youtube.com/user/aschmelyun) or my [Twitter](https://twitter.com/aschmelyun).

If you have any questions at all, don't hesitate to get in touch!