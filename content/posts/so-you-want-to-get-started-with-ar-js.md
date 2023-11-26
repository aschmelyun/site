---
view: layout.post
title: So you want to get started with AR.js
cover_image: https://thepracticaldev.s3.amazonaws.com/i/khxn390gis1qehppsjg7.jpg
description: Augmented reality directly on a mobile browser seems pretty far-fetched, but it's easy to add with the AR.js library!
categories: javascript
published: May 29 2019
excerpt: Augmented Reality seems like it’s everywhere. Between Snapchat filters, Google stickers, IKEA’s furniture preview, and now Shopify’s AR Quick Look feature, putting 3D objects in the physical world seems to be more desirable than ever.
---

Augmented Reality seems like it’s everywhere. Between Snapchat filters, Google stickers, IKEA’s furniture preview, and now Shopify’s [AR Quick Look](https://medium.com/shopify-vr/ar-shopping-gets-simpler-with-ar-quick-look-on-shopify-c2716593823f) feature, putting 3D objects in the physical world seems to be more desirable than ever.

While an augmented reality feature might fit nicely into an established native application, there’s a downside to those non-app-based businesses who’d like to use AR for marketing purposes. For instance, say a handbag company wants to display a 3D model of a handbag above the box it ships in when the user points their camera at their logo on the box. Traditionally, they’d have to:

- Develop a native application using ARKit/ARCore/ARToolkit/etc
- Pay the requested fees to get it distributed to the Google Play/App Stores
- Wait for approval from either of the above stores
- Market the application to your prospective customers/users
- Hope that the user downloads and then uses the app

All of this for what amounts to (on average) a 2–5 minute session playing around with the finished app. Additionally if it’s for a specific marketing campaign or time span, they’re more than likely not going to interact with it more than a few times.

The solution? **[AR.js](https://github.com/jeromeetienne/AR.js/blob/master/README.md)**, simple augmented reality directly in the browser and compatible with all modern mobile devices. Now, let’s get started!

## What is AR.js?

To boil it down, it’s essentially a Javascript framework acting as a port of ARToolkit, while leveraging other packages like [a-frame](https://aframe.io/) and [three.js](https://threejs.org/). The goal being augmented reality features directly on web browsers without sacrificing performance.

As far as compatibility goes, it works on any browser capable of WebGL + WebRTC. At the publishing time of this article, that would be Chrome, Firefox, Edge, and Safari. However, the current version of Chrome for iOS is **not** supported as of yet.

## Getting a project set up

This assumes that you have a (local or otherwise) development environment already set up and secured with an SSL certificate. **Why SSL?** *Chrome requires all sites that use scripts calling for camera access to be delivered strictly over https.*

Following from [Alexandra Etienne’s](https://medium.com/@AndraConnect?source=post_header_lockup) article we can have a working demo of AR.js up in just 8 lines of HTML.

![Screenshot of a simple AR.js code demo](https://cdn-images-1.medium.com/max/1600/1*9JPgSiBdvtWyx9ELbLZ2ew.png)

Throw that bad boy into an index.html file on your dev environment and open it up in your compatible browser of choice. You’ll get a request to access your camera, and on approval will see a live video feed from your device. Print out/load up the [default hiro marker image](https://github.com/jeromeetienne/AR.js/blob/master/data/images/hiro.png) and point it at the video feed. If everything went well, you should see a semi-clear white cube fixed over the marker.

Augmented reality, **on the web!**

Want to remove the debug boxes from the video display? Simply add the flag ‘debugUIEnabled: false;’ to the arjs prop in the `a-scene` element.

![Screenshot of disabling the debugUI in AR.js](https://cdn-images-1.medium.com/max/1600/1*Sxab5E4oSareaJ2i87NLmQ.png)

## Using custom markers

This was honestly one of the most difficult pieces for me to get working properly when I was originally learning AR.js. There’s a few tutorials out there about how to get custom markers working, but between them there’s some conflicting information. I’m going to give the run-down of what worked best for me, broken down step by step.

**Step 1. Decide on your marker image.** For the best results this should be a simple, high-contrast, PNG image pre-cropped to be a square with a size of at least 512x512. This also has to be **rotationally asymmetrical**, meaning that it will always look unique regardless of the degree it’s rotated to. Here’s some good examples of marker images:

![Example marker images for AR.js](https://cdn-images-1.medium.com/max/1600/1*zUxIO_eUSMyDg4Nq0FPY1g.png)

**Step 2. Convert your image to a .patt file and an AR.js marker.** Using [this marker trainer](https://jeromeetienne.github.io/AR.js/three.js/examples/marker-training/examples/generator.html), upload an image that you’d like to use as a marker. If it looks good to you, then go ahead and click:

- `Download Marker:` The .patt file AR.js uses to recognize the marker and display your 3D content.
- `Download Image:` The actual .png image with the appropriate black border to be used as the physical marker.

**Step 3. Add the .patt file to your website.** It really doesn’t matter where as long as it’s accessible to the application, I usually put mine in my assets folder using a structure like `/img/patterns/my-marker.patt`.

**Step 4. Add the marker to your AR.js element.** Using the a-frame method, this is very simple. All you need to do is add an `a-marker ` element with a type prop of ‘pattern’, and the relative path to your pattern file as the ‘url’ prop.

Using this method we will also need to include an `a-entity camera` so that the virtual camera is included in the 3D environment being rendered.

![Screenshot of adding in a custom marker in AR.js](https://cdn-images-1.medium.com/max/1600/1*cyOLiJDEH0qlLOKOZJXaQw.png)

**Step 5. Finally, add in the 3D model you would like to display. To complete this, we need to add in a model that’ll be shown when this marker is discovered. Thankfully, a-frame includes a bunch of primitive geometries that we can easily drop-in using some simple element names.

- `<a-box></a-box>`
- `<a-sphere></a-sphere>`
- `<a-cylinder></a-cylinder>`
- `<a-plane></a-plane>`

Each of these have a position prop that can be adjusted to allow you to move it around the 3D space relative to the marker position. This way your objects don’t need to sit directly on the marker but can float above it, or off to the side. For this example, I’m going to add in a red sphere that floats just slightly above the marker.

![Adding a custom 3D model with custom marker to an AR.js app](https://cdn-images-1.medium.com/max/1600/1*zSw6QgOr9Ok7wYaTFlSuSQ.png)

**Boom** 💥

Replace the previous boilerplate with the new custom pattern code, and show your downloaded marker image to the camera. You should see a red sphere floating just above the marker icon!

![Demo of a red sphere hovering above the Hiro marker using AR.js](https://cdn-images-1.medium.com/max/1600/1*sVsjfTeLUND_hwEFx-JAOw.png)

This might seem pretty simple, but when you consider that we did this in just a dozen lines of HTML the power and possibility of using AR.js really shines through.

**You can stop here if your goal was to just learn about the fundamentals.**
From here on out I'm going to show you a little more advanced tips + tricks for customizing AR.js to work within a variety of needs.

## Shrink the size of the black marker border

Personally I think that the default border thickness is a little jarring to see on markers, and I’ve heard the same thing parroted by a few other people using AR.js. However, if you’re using the latest version of the framework, it’s easier than ever to adjust the border size to your preference!

![Difference in border thickness for AR.js markers](https://cdn-images-1.medium.com/max/800/1*UjR6nsfKSoAChKN3lJzncw.png)

Which looks better? Left: 0.5, Right: 0.8

I discussed during the last article about how to generate markers and their images using the [AR.js Marker Training tool](https://jeromeetienne.github.io/AR.js/three.js/examples/marker-training/examples/generator.html). If you visit the link, you’ll see that in the top-left corner there’s a little slider for **Pattern Ratio** (hint: that’s the black border thickness). You can think of it as ‘Percentage of the marker taken up by the actual marker image’. So for instance, if the Pattern Ratio is set to 0.75 (my preferred value), that means that 75% of the marker is your image in the center, and the remaining 25% is taken up by the black border.

Once you’ve nailed down where you want your pattern ratio to be, generate and save both your marker pattern and marker image for your app as detailed previously in this article. Back in your app, all it takes is one small adjustment to tie this in. On your <a-scene> element, add in `patternRatio=0.75` (or whatever your desired value is) to the **arjs** prop.

![Screenshot of custom pattern ratio for an AR.js marker](https://cdn-images-1.medium.com/max/800/1*ctvKA6qOh08YFP2zAT7AdA.png)

## Use your own 3D models

Sure cubes, spheres, planes, and cylinders are cool, but most of the time you’re going to want to utilize and display a custom 3D model in the augmented reality scene you’re creating. Luckily AR.js makes that a pretty simple endeavor!

The easiest way to get started with this, would be to make sure your models are either in **obj** or **glTF** formats. These work natively with AR.js and a-frame, requiring zero additional setup or configuration to get started. You can find a huge repository of free and affordable obj models on [https://sketchfab.com](https://sketchfab.com).

**Note:** In the following examples you’ll see the `<a-entity>` tag, this is a generic replacement for `<a-sphere>` and the like, allowing you to specify your own geometries/materials/etc instead of using prefabbed ones.

### For obj models:

Inside of our a-entity tag, we’ll be using the `obj-model` prop, which will require you to specify paths to both the **.obj** model file and the accompanying **.mtl** material file. The end result should look like this:

![Screenshot of an obj model in AR.js](https://cdn-images-1.medium.com/max/800/1*62s3moLx4zQyoudEb66Kkw.png)

### For glTF models:

This one’s even easier, because it’s just one path! Swap out the obj-model prop for `gtlf-model` and supply the path to your model as the value:

![Screenshot of a glTF model in AR.js](https://cdn-images-1.medium.com/max/800/1*xuE0S9Qjds07I0tTPaKkOQ.png)

## Create an event listener for the markers

Why would we want an event listener in the first place? I can give you a real-world example: My client wanted to display a simple block of content whenever a marker was active on a user’s device. The content was supposed to disappear whenever there was not a marker currently active. In order to implement this we needed to add in an event listener that would fire whenever a marker was found/lost, and then we’d hook into that in our main site’s JavaScript bundle and display/hide the content whenever that event was fired.

To implement the event listeners you’ll just register an aframe component and then set the event listeners for markerFound and markerLost. Inside their respective callback functions, write any JS you want that’ll be fired when a marker is found or lost:

![Screenshot of implemented event listeners for markers in AR.js](https://cdn-images-1.medium.com/max/800/1*DDBk985LRyE8G_9blxnoCg.png)

**That’s all for now!** If you have any questions/comments on this article or anything AR.js related, feel free to drop me a line on [Twitter](https://twitter.com/aschmelyun) 🐦.