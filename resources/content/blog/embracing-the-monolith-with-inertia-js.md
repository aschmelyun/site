---
view: layout.post
title: Embracing the monolith with Inertia.js
description: By using Inertia, I'll show how you can bridge the gap between traditional backends and modern frontend frameworks like React and Vue.
categories: javascript
published: Sep 14 2023
excerpt: It's likely that your single-page applications could work more effectively as a monolith, bundled together with backend code. How's this possible? We can use a bridge to span the gap between traditional backends and modern SPA frameworks. Let's talk Inertia.js.
---

It's likely that your single-page applications could work more effectively as a monolith, bundled together with backend code. Let me explain a little more, and provide some solid context.

When building a new application decisions need to be made about architecture, especially when using a frontend framework like React or Vue. At minimum you'll need an API layer to handle data, likely authentication, maybe additional layers for roles and permissions, or communicating with third-party services, plus assets, queues, and the list goes on.

For these reasons the [microservices](https://en.wikipedia.org/wiki/Microservices) architecture pattern has become fairly popular. Split everything apart into individual services and it's easier to develop, scale, and maintain, right?

Well, not always.

In fact, I'll go as far to say that for a lot of applications this pattern tends to be overkill. For most applications with a moderate user base and development team size, a single monolithic application containing all the code powering your frontend, backend, authentication, assets, and everything else, enables you to work more effectively. Each microservice has its own requirements, tooling, and build processes that can cause overlap between codebases, which can lead to maintainability problems or out-of-sync dependencies.

Instead, we can use a bridge to span the gap between traditional backends and modern SPA frameworks. **Let's talk Inertia.js.**

## What is Inertia?

[Inertia](https://inertiajs.com) is a _framework agnostic library_ that allows you to build single page applications using classic server side routing and controllers. It works by automatically providing your backend framework with the ability to conditionally return either a compiled view or a JSON response from your controller actions. Inertia then automatically uses either the view or the JSON to hydrate and update the frontend, seamlessly.

Despite it's relatively complex documentation, it's a fairly simple concept and small underlying library. Before we get into the specifics of how it works, let's first address the biggest issue that it attempts to solve.

## The redundancy problem

In a traditional single page application, you have a your frontend framework (Vue, React, Svelte, etc) and then usually some sort of backend API that serves data which the frontend can consume and display. This is a pretty standard approach and it's the way I've built most of my SPAs in the past. But, it has a glaring flaw, you're now maintaining **two separate applications**.

What do I mean? Well, let's take an example of a practical application: car inventory.

In this example you might have a page called like `/cars` that returns back a component to render a bunch of car models. This component, once mounted, makes a request to `/api/cars` to get all of the car data that's needed to populate the page. We have repitition, two routes for essentially the same thing, displaying a page with car data on it.

That repetition likely happens on every route, as you need relevant data associated with the current page to be grabbed from the API layer, and then displayed on the frontend. So now if I wanted to add or update a route, I have to do it in _multiple_ different places.

As more pages or data points are added, this increases the complexity and scope in both the frontend and backend. This in turn increases the chance of disconnects and redundency happening between the two applications. So, how can we help that?

## Back to the monolith

What if instead we could have the functionality of an SPA, but with our frontend application code _bundled together_ with the backend? Truely a monolithic architecture while providing the beauty and sleekness of an SPA. That's what Inertia enables.

Instead of using a frontend routing library like react-router or vue-router, you use the routing provided by your backend framework to handle incoming requests. Inertia intelligently determines the purpose of the visit and returns back either a compiled view, or just the data needed to populate the next page component.

The end result is a super smooth experience for both the end user, but also _the developer_. Instead of having to manage a separate codebase or repository for both your frontend and backend code, your frontend exists _alongside_ your backend code, close by in a separate directory. No routing libraries for navigation and no separate API routes just to populate the frontend.

Want to start using this in your own projects?

## How to get started

Inertia is a framework agnostic library, meaning it can be used with any backend or frontend framework. However, there are a few first-party adapters that make it easier to get started.

For the backend these include:

- [Laravel](https://github.com/inertiajs/inertia-laravel)
- [Rails](https://github.com/inertiajs/inertia-rails)

And for the frontend:

- [React](https://www.npmjs.com/package/@inertiajs/inertia-react)
- [Vue](https://www.npmjs.com/package/@inertiajs/inertia-vue)
- [Svelte](https://www.npmjs.com/package/@inertiajs/inertia-svelte)

If we have a Laravel project and we want to build a React frontend with it, we can install the Laravel adapter and the React adapter:

```bash
composer require inertiajs/inertia-laravel
npm install @inertiajs/react react react-dom
```

Then, we can update our main app's JavaScript file to look something like this:

```js
import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.jsx', { eager: true })
        return pages[`./Pages/${name}.jsx`]
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    }
});
```

Using the above code, you'll add new components representing individual pages under a `Pages` directory. These will be automatically bundled and served whenever a route is requested, all we need to do is ensure the route returns an Inertia response object.

In Laravel, that might look like this:

```php
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard'); // located in Pages/Dashboard.jsx
});
```

If we create that file and then visit the route, we should see our rendered component! Which is pretty cool, but, how's this actually work?

## Under the hood

Inertia's documentation is a great resource for deeply understanding the protocol behind how it works, but I'll try to summarize it here.

When you make an initial request to your website, regardless of the route, Inertia and your backend framework return back a response consisting of a compiled view with whatever props passed in through a `data-page` attribute. It might look like this:

```html
<div id="app" data-page="{&quot;component&quot;:&quot;Dashboard&quot;,&quot;props&quot;:{&quot;errors&quot;:{},&quot;user&quot;:{&quot;id&quot;:1,&quot;name&quot;:&quot;Andrew&quot;}}}"></div>
```

This is then used with the JavaScript code shown above to begin hydrating the frontend and building out the DOM that becomes your application. The `data-page` attribute JSON is parsed out and used to populate any props defined in to your React or Vue page component.

The magic happens when we navigate to a new page.

When you click a link in your application, Inertia's frontend code hijacks that event and makes the request in the background instead. By passing in a custom header attribute, your backend code recognizes that it's a subsequent request. Instead of returning back a compiled view like our initial visit, your application returns back just a JSON object consisting of the component that needs to be loaded and the data props associated with it.

It might look something like this:

```json
{
    component: "Dashboard",
    props: {
        errors: {},
        user: {
            id: 1,
            name: "Andrew"
        }
    }
}
```

Inertia's frontend takes that response, swaps out the DOM using the component passed in, populates the props attributes, and then modifies the url and browser history to maintain state.

That's it!

Everything else is up to either the backend framework or your frontend library to handle how that data is displayed, and the component used on any given page request. **Inertia acts just as a protocol, a broker between the two layers to make routing and data binding much easier.**

## Caveats

I don't want this article to be _just_ rainbows and sunshine, there are some use cases where Inertia _might not_ be the right pick.

For example, if you're building out a backend that will likely be consumed by other applications besides a web frontend (native mobile app, desktop application, third-party integrations), then Inertia might not be a solid choice. Since you're likely building out API routes anyway for these other distributions, separating out your frontend SPA code and backend API application makes a bit more sense.

Additionally, Inertia doesn't play super well with things like PWAs and offline-first practices. A decent amount of extra development would be needed to make offline route caching work more effectively, because normally it's your _backend_ code that handles routing. If your application experiences network delay or interruption, your application essentially doesn't know how to process the next request.

## Wrapping up

Embracing the monolith architecture can be a solid choice for your application, and Inertia is a match made in heaven for marrying your application's frontend and backend code more effectively. Simply put, it's enabled me to rapidly build out full-stack applications in less time with less code redudency.

For more information I recommend checking out the Inertia documentation, or the Getting Started with Inertia series on Laracasts. If you'd like to talk more about building applications with Inertia or how you might convert your existing Laravel microservices application to a monolith, feel free to reach out!