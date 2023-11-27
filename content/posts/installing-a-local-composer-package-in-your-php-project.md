---
title: Installing a local Composer package in your PHP project
slug: installing-a-local-composer-package-in-your-php-project
description: Using Composer is a straightforward experience, but adding in a local package for use in a PHP project with it can sometimes be difficult
categories: php
published_at: Jan 22 2022
excerpt: If you've worked in a PHP project, chances are you've dealt with the Composer package manager. As a full-stack developer, I think it's one of the better ones that I use on a regular basis, consistently improving while remaining relatively simple. One of the more difficult things to do with it though, is adding a local package for use in a larger PHP project.
---

If you've worked in a PHP project, chances are you've dealt with the [Composer](https://getcomposer.org) package manager. As a full-stack developer, I think it's one of the better ones that I use on a regular basis, consistently improving while remaining _relatively_ simple.

One of the more difficult things to do with it though, is **adding a local package for use in a larger PHP project**. Whether you've downloaded a private source, or are developing a package locally, this method will work to get your package into Composer.

First, open up your PHP project's `composer.json` file. You're going to want to add a repositories array if one isn't already present. That array expects a list of package sources, and we're going to provide our local package's directory as one.

```json
"repositories": [
    {
        "type": "path",
        "url": "./packages/aschmelyun/my-package",
        "options": {
            "symlink": true
        }
    }
],
```

Passing in the `"symlink": true` option means that our package's source folder will be symlinked into the `vendor` directory of our PHP project.

You might be asking: **Why don't we just add the source of our package right into the vendor directory?**.

You're right, we _could_ do that and it _might_ work. But keeping everything contained in the Composer ecosystem ensures that our project dependencies run smoothly. It also opens up the door to use other features like multiple packages in the same source repo, or ensuring our local package meets dependency requirements.

Okay, after that's done, we just need to update our `require` list with our package:

```json
"require": {
    "aschmelyun/my-package": "@dev"
```

The `@dev` version string ensures that no matter what, the source code that we have in our package folder _should_ be what gets added in and referenced in our PHP project.

The package name that we use in the above should match the `name` attribute in the `composer.json` of the PHP package, **not just the folder path**. For instance, here's what the `composer.json` might look like for the above test package:

```php
{
    "name": "aschmelyun/my-package",
    "description": "Just a test package",
    "require": {
        "php": "^7.2 || ^8.0"
    },
    "autoload": {
        "psr-4": {
            "ASchmelyun\\MyPackage\\": "src/"
        }
    },
    "minimum-stability": "dev",
    "prefer-stable": false
}
```

The last two attributes in the above file aren't _usually_ required, but could help if you run into any errors getting your local package to load into Composer.

Finally, all you have to do is update your Composer packages from the project root:

```bash
composer update
```

✨ **Ta-da!**

Now you can use your package in your PHP project just like you would if you installed it through `composer require ...`.

Have any questions about web development, or would like to see more shorter-form content from me? Follow me on Twitter [@aschmelyun](https://twitter.com/aschmelyun)!