---
title: Authenticating a Vue SPA is easy with Laravel Sanctum
slug: authenticating-a-vue-spa-is-easy-with-laravel-sanctum
description: Laravel Sanctum is a featherweight package for authenticating Vue, React, and native mobile applications to a Laravel backend.
categories: laravel, vue
published_at: May 11 2020
excerpt: Released earlier this year, Laravel Sanctum (formerly Laravel Airlock) is a lightweight package to help make authentication in single-page or native mobile applications as easy as possible. Where before you had to choose between using the web middleware with sessions or an external package like Tymon's jwt-auth, you can now use Sanctum to accomplish both stateful and token-based authentication.
---

Released earlier this year, [Laravel Sanctum](https://laravel.com/docs/7.x/sanctum) (formerly Laravel Airlock), is a lightweight package to help make authentication in single-page or native mobile applications as easy as possible. Where before you had to choose between using the web middleware with sessions or an external package like Tymon's jwt-auth, you can now use Sanctum to accomplish both stateful and token-based authentication.

In this short walk-through, I'll show you how to get started with the former. We'll create a dummy API, authenticate a user from a Vue component, and get data associated with that signed-in user.

If you'd like to skip the written tutorial, you can check out [this video](https://www.youtube.com/watch?v=eeMtmkDZ72Q) I've made instead. You can also go directly to the finished source code, available in [this repository](https://github.com/aschmelyun/video-auth-vue-laravel-sanctum) on GitHub.

Alright, let's dive in!

## Creating a test API

The first thing we'll need to do is create an API that we can get data from. I'm thinking of a super simplistic app that will retrieve a list of secrets associated with our user.

I've installed an out-of-the-box Laravel app, and have it and a MySQL database set up in a local environment using my [Laravel Docker setup](https://github.com/aschmelyun/docker-compose-laravel). The first thing that I'm going to create is a model and migration for our secret. Using the command line, this is easy with artisan.

```bash
php artisan make:model Secret --migration
```

Let's open up that migration file and add in our data columns needed for a secret. I think all we'll need from this (aside from the default ID and timestamps that Laravel provides) is a user_id integer to form the relationship to a user, and the actual secret.

```php
Schema::create('secrets', function (Blueprint $table) {
    $table->id();
    $table->integer('user_id');
    $table->text('secret');
    $table->timestamps();
});
```

Okay, running the migration will bring up our `users` and `secrets` tables:

```bash
php artisan migrate
```

A couple of quick modifications will need to be made to both of our app's models enabling the relationships, so let's open them up and get started:

```php
// User.php

public function secrets()
{
    return $this->hasMany('App\Secret');
}
```

```php
// Secret.php

public function user()
{
    return $this->belongsTo('App\Secret');
}
```

The final piece of our API structure is the actual routes and controllers. We're only going to be accessing a single path, which will retrieve the secrets associated with our user. So, I've added the following to my app's `routes/api.php` file:

```php
Route::get('/secrets', 'SecretController@index');
```

The controller for this can be created easily using an Artisan command:

```bash
php artisan make:controller SecretController
```

Opening up that controller, let's create our index method and just return back all of our app's secrets for now. Since we have no way of getting an authenticated user, **yet**:

```php
public function index()
{
    return App\Secret::all();
}
```

Our dummy API is done for now, let's create some fake users and secrets.

## Populating the database

You can easily go right into the database and populate it manually, create a controller and form for users to input their own data, or use Artisan tinker to semi-automate user creation. I'm going to skip these methods and instead use the built-in Laravel factories to generate fake data for our users and secrets.

Out of the box, Laravel comes with a `UserFactory.php` class, to generate dummy users. We're going to create one for our secrets as well. In your terminal, run the following Artisan command:

```bash
php artisan make:factory SecretFactory --model=Secret
```

Open up the generated file, and we're going to need to populate each model with just two data points. Our user_id, and a secret:

```php
$factory->define(Secret::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'secret' => $faker->text
    ];
});
```

You might be wondering why we're hard-coding in our `user_id` in the above snippet. Instead of generating it randomly based on the amount of users, I want to have more control over it. I'll show you shortly how I can overwrite this when we start generating our secrets.

Let's start by creating a couple of fake users. Open up your tinker shell by running the `php artisan tinker` command from your site root. Once it's open, we can create two users by running the global factory helper twice:

```php
factory(App\User::class)->create(); //create saves our user in the database, unlike make
```

Now that we have them generated, let's create our secrets. I'm going to run the following in the tinker shell twice to create two for `user_id 1`:

```php
factory(App\Secret::class)->create();
```

But what about our second user with a different id? Overwriting any of the values in our factory class is easy, all we do is pass in an override array to the `create()` method. So, we'll run the following twice to create two secrets for our second fake user:

```php
factory(App\Secret::class)->create(['user_id' => 2]);
```

Our database is populated with enough fake data, let's move on to installing and prepping the Laravel Sanctum package.

## Installing Laravel Sanctum

Installation is a breeze, and can be accomplished by running a few commands in our terminal. First, let's actually install the package using Composer:

```bash
composer require laravel/sanctum
```

Then we'll have to publish the migration files (and run the migration) with the following commands:

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

The last part of Sanctum's installation requires us modifying the `app\Http\Kernel.php` file to include a middleware that will inject Laravel's session cookie into our app's frontend. This is what will ultimately enable us to pass and retrieve data as an authenticated user:

```php
'api' => [
    EnsureFrontendRequestsAreStateful::class,
    'throttle:60,1'
]
```

Now, we can move onto our app's frontend!

## Building the frontend

Starting with **Laravel 7**, the frontend and authentication boilerplates were stripped out of the main package and instead can be installed with a separate bundle. For the sake of this demonstration, we're going to use it and Vue to craft our frontend.

Running the following commands from our app's root will help us get that set up:

```bash
composer require laravel/ui
php artisan ui vue --auth
npm install && npm run dev
```

These commands do three things:

1. Install the Laravel UI package with Composer

2. Generate the JS/UI files, auth boilerplate, and package.json modifications

3. Install the frontend dependencies and compile development JS/CSS assets

I'm going to copy over everything in the `welcome.blade.php` file to an `app.blade.php` file. In it, I'll be stripping out the interior content and adding an **id="app"** attribute to the outside div. This will be our Vue app's mount point, as detailed in the generated app.js file.

Let's create our Vue component that will hold our login form and display some secrets.

## Creating a Vue component

When we generated our frontend code earlier using **php artisan ui vue**, an example component was generated under `resources/js/components/ExampleComponent.vue`. Let's create a brand new component called `SecretComponent.vue`, with a basic data structure:

```vue
<template>

</template>
<script>
export default {
    data() {
        return {
            secrets: [],
            formData: {
                email: '',
                password: ''
            }
        }
    }
}
</script>
```

Our secrets are kept in an array, and we also have a formData object that will be used to store the email address and password for our login form.

Speaking of which, let's get started building out the markup that will create that form. Expanding between the `<template>` tags a bit, we'll add two divs. One for the login form, which will be hidden when there's secrets available, and another for the list of secrets, which will be hidden *until* there's secrets.

```vue
<template>
    <div>
        <div v-if="!secrets.length" class="row">
            <form action="#" @submit.prevent="handleLogin">
                <div class="form-row">
                    <input type="email" v-model="formData.email">
                </div>
                <div class="form-row">
                    <input type="password" v-model="formData.password">
                </div>
                <div class="form-row">
                    <button type="submit">Sign In</button>
                </div>
            </form>
        </div>
    </div>
</template>
```

Now, we should have a good-looking sign-in form:

![Screenshot of a login form made with Bootstrap](https://dev-to-uploads.s3.amazonaws.com/i/m9kz2vgr6s2p983upf9v.png)

In the code above, we're excluding an action from the form, and instead using Vue's submit handler to take care of the form submission. Let's create the method called `handleLogin`, which will be fired whenever a user tries to sign in:

```vue
<script>
export default {
    data() {
        return {
            secrets: [],
            formData: {
                email: '',
                password: ''
            }
        }
    },
    methods: {
        handleLogin() {
            // handle user login
        }
    }
}
</script>
```

Finally, we can go into our `resources/js/app.js` file and register our new component:

```js
Vue.component('secret-component', require('./components/SecretComponent.vue).default);
```

Then going back into our `app.blade.php` file, replacing **<example-component>** with **<secret-component>**. Let's put that `handleLogin()` method to use and authenticate a user!

## Authenticating a user

If we take a look at the [Laravel Sanctum documentation](https://laravel.com/docs/7.x/sanctum#spa-authentication) for SPA authentication, it details that we first need to make a call to a route at `/sanctum/csrf-cookie`, which will set the CSRF protection on our app and enable POST requests uninterrupted.

From there, we can proceed to send a request to the `/login` route, provided by the authentication framework we added in earlier. All we have to do is pass in our user's email and password, and it will authenticate our user if the credentials match.

Let's try implementing that in our `handleLogin()` method from earlier:

```js
handleLogin() {
    axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post('/login', this.formData).then(response => {
            console.log('User signed in!');
        }).catch(error => console.log(error)); // credentials didn't match
    });
}
```

Okay, if we save that and try to sign in with one of the dummy users in our database, everything should go through smoothly! The first request sets the CSRF protection in our frontend, and the second one sends over the formData object containing our user's email and password. No response data is sent back over, so we can just continue on using a Promise's resolve.

What's there to do now? Well, let's get our user's secrets!

## Retrieving user data

In our Vue component, let's create a new method called `getSecrets()`, which will make a call to our secrets API endpoint that we created earlier. If everything goes successfully, that call should return back an array of secrets, which we can use to replace the array in our component's data object.

It will need to be called after our user has successfully logged in, so the flow will look something like this:

```vue
handleLogin() {
    axios.get('/sanctum/csrf-cookie').then(response => {
        axios.post('/login', this.formData).then(response => {
            this.getSecrets();
        }).catch(error => console.log(error)); // credentials didn't match
    });
},
getSecrets() {
    axios.get('/api/secrets').then(response => this.secrets = response.data);
}
```

But wait, we're returning **all** of the app's secrets, remember? Not just the user's. We can change that with a single line though, thanks to Laravel Sanctum. Let's open up our SecretController and navigate to the `index()` method, making a few changes:

```php
public function index(Request $request)
{
    return $request->user()->secrets;
}
```

Included in each API call (after we've successfully logged our user in), is a `laravel_session` cookie in the header of each request. Sanctum takes this cookie, and using the fact that our API endpoint is stateful due to the added-in middleware from earlier, and determines if a user is logged in.

This user in its entirety, can be retrieved using the `$request` object. We can then chain the secrets relationship onto it, returning back the array of our user's secrets.

Finally, let's add in some boilerplate markup to render out these secrets to our user:

```vue
<template>
    <div>
        <div v-if="secrets.length" class="row">
            <div class="secret" v-for="(secret, index) in secrets" :key="index">
                <strong v-text="secret.secret"></strong> - created at <span v-text="secret.created_at"></span>
            </div>
        </div>
    </div>
</template>
```

💥 Now if we refresh our app and sign in using our fake user's credentials, we'll see a list of our user's secrets displayed out for us:

![Screenshot of generated text making up a secrets list](https://dev-to-uploads.s3.amazonaws.com/i/kxufrlm58rvez6w5b93g.png)

So, now what's left?

## Conclusions and next steps

I've only just scratched the surface of what this incredibly powerful and easy-to-use package offers. If you're using the techniques above to authenticate a user in an SPA, the entire time they're on your app after being authenticated, you can make calls to your accompanying Laravel API as if you were a logged-in user in a traditional web app.

Additionally, you can use token-based authentication for standalone SPAs, native mobile applications, or something like ElectronJS desktop apps. Everything is handled in such an eloquent way, and the [documentation](https://laravel.com/docs/7.x/sanctum) around the package is incredibly robust.

I hope this article's helped you get started authenticating users into your Vue SPA with Laravel. As always, if you have any questions please don't hesitate to reach out to me in the comments below or on my [Twitter](https://twitter.com/aschmelyun).