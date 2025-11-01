# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel-based personal website/blog built with a file-based content management approach using Orbit (flat-file driver for Eloquent). Content is stored as Markdown files and served through Laravel with Tailwind CSS for styling.

## Development Commands

### Building Assets
```bash
npm run dev          # Start Vite development server with hot reload
npm run build        # Build production assets
```

### PHP/Laravel
```bash
php artisan serve    # Start local development server
php artisan orbit:cache  # Cache all Orbit models (run after content changes)
```

### Testing
```bash
./vendor/bin/phpunit              # Run all tests
./vendor/bin/phpunit --filter=TestName  # Run specific test
```

### Code Quality
```bash
./vendor/bin/pint    # Format PHP code (Laravel Pint)
```

## Architecture

### Content Management with Orbit

The site uses **Orbit** (ryangjchandler/orbit) as a flat-file driver for Laravel Eloquent. This allows models to be backed by Markdown files instead of a database:

- **Models** (Post, Project, Course) use the `Orbital` trait and define schemas in-code
- **Content files** are stored in `/content/posts/`, `/content/projects/`, `/content/courses/`
- Each Markdown file has YAML frontmatter containing model attributes (title, slug, description, categories, published_at, etc.)
- The file body contains the main content (rendered using graham-campbell/markdown)
- Models use `slug` as the primary key (non-incrementing)
- Run `php artisan orbit:cache` after adding/modifying content files to refresh the cache

### Content Structure

Markdown files follow this pattern:
```markdown
---
title: Post Title
slug: post-slug
description: Post description
categories: category1, category2
published_at: May 21 2022
excerpt: Short excerpt text
---

Main content body in Markdown...
```

### Key Models

- **Post** (`app/Models/Post.php`): Blog posts with published_at timestamps
- **Project** (`app/Models/Project.php`): Portfolio projects with github_stars and external links
- **Course** (`app/Models/Course.php`): Course listings with thumbnails and links

All models use the Orbital trait and define their schema via `schema(Blueprint $table)` method.

### Routing

Routes are defined in `routes/web.php`:
- Landing page shows 5 most recent posts
- All other routes use `AddTrailingSlash` middleware
- Blog posts use route model binding with slug: `/blog/{post:slug}`

### Markdown Rendering

- Uses `graham-campbell/markdown` for parsing
- Syntax highlighting via Torchlight (torchlight/torchlight-laravel)
- Content is converted in controllers: `Markdown::convert($post->content)->getContent()`

### Frontend

- **Tailwind CSS** with typography plugin for prose styling
- Custom fonts: Poppins (sans), DM Serif Display (serif)
- Vite for asset bundling
- Entry points: `resources/css/app.css`, `resources/js/app.js`

### Views

Blade views organized by content type:
- `resources/views/landing.blade.php` - Homepage
- `resources/views/posts/` - Blog listing and detail pages
- `resources/views/projects/` - Project listing
- `resources/views/courses/` - Course listing
- `resources/views/components/` - Reusable components including Layout component
