# Laravel Filter Package

[![Build Status](https://img.shields.io/github/actions/workflow/status/brann-meius/laravel-filter/ci.yml)](https://github.com/brann-meius/laravel-filter/actions)
[![codecov](https://codecov.io/github/brann-meius/laravel-filter/graph/badge.svg?token=36QKLEBBTV)](https://codecov.io/github/brann-meius/laravel-filter)
[![License](https://img.shields.io/github/license/brann-meius/laravel-filter)](LICENSE)

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
- [Installation](#installation)
- [Usage](#usage)
    - [Creating Filters](#creating-filters)
    - [Applying Filters](#applying-filters)
    - [Caching Filters](#caching-filters)
    - [Example](#example)
    - [Using `ExcludeFor` and `OnlyFor` Attributes](#using-excludefor-and-onlyfor-attributes)
    - [Example Request Structure](#example-request-structure)
    - [Advanced Usage](#advanced-usage)
- [Examples for Other Databases](#examples-for-other-databases)
- [Extending Logic](#extending-logic)
- [Support](#support)
- [License](#license)

## Overview

The `meius/laravel-filter` package provides a convenient way to apply filters to Eloquent models in a Laravel application. It allows you to define filters using attributes and apply them dynamically based on the request.

## Requirements

- PHP \>= 8.0
- Laravel \>= 8.0

## Getting Started

To get started with the `meius/laravel-filter` package, follow the installation instructions below and check out the usage examples.

## Installation

1. Install the package via Composer:
    ```bash
    composer require meius/laravel-filter
    ```

## Usage

### Creating Filters

1. To create a new filter, use the `make:filter` Artisan command:
    ```bash
    php artisan make:filter {name}
    ```

### Applying Filters

1. Use the `Filterable` trait in your controller class:
    ```php
    use App\Http\Controllers\Controller as BaseController;
    use Meius\LaravelFilter\Traits\Filterable;

    class PostController extends BaseController
    {
        use Filterable;

        // Your methods
    }
    ```

2. Define filters using attributes in your controller methods:
    ```php
    use App\Attributes\Filter\ApplyFiltersTo;
    use App\Models\Post;

    class PostController
    {
        use Filterable;

        #[ApplyFiltersTo(Post::class)]
        public function index()
        {
            return Post::query()->get();
        }
    }
    ```

3. To apply filters to related models, use the `ApplyFiltersTo` attribute with multiple model classes:
    ```php
    use App\Attributes\Filter\ApplyFiltersTo;
    use App\Models\Author;
    use App\Models\Comment;
    use App\Models\Post;

    class PostController
    {
        use Filterable;

        #[ApplyFiltersTo(Post::class, Comment::class, Author::class)]
        public function index()
        {
            return Post::query()
                ->with([
                    'comments', 
                    'author',
                ])
                ->get();
        }
    }
    ```

### Caching Filters

1. To cache the filters for faster loading, run the following Artisan command:
    ```bash
    php artisan filter:cache
    ```

### Example

Here is an example of how to define and apply filters:

1. Create a filter:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        /**
         * The key used to identify the filter parameter in the request.
         */
        protected string $key = 'title';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('title', 'like', "%$value%");
        }
    };
    ```

2. Create a filter for related models:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        protected string $key = 'author_id';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->whereHas('author', function (Builder $query) use ($value) {
                $query->where('id', '=', $value);
            });
        }
    };
    ```

3. If you need to apply a filter according to a condition, you can use the `canContinue` method:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        /**
         * The key used to identify the filter parameter in the request.
         */
        protected string $key = 'owner';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('user_id', '=', $value);
        }

        protected function canContinue(Request $request): bool
        {
            return $request->user()->hasSubscription();
        }
    };
    ```

### Using `ExcludeFor` and `OnlyFor` Attributes

You can use the `ExcludeFor` and `OnlyFor` attributes to conditionally apply filters.

1. Create a filter with `ExcludeFor`:
    ```php
    use App\Models\User;
    use App\Models\Category;
    use Meius\LaravelFilter\Attributes\ExcludeFor;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will never be applied to the "User" model and beyond.
    return new #[ExcludeFor(User::class, Category::class, ...)] class extends Filter
    {
        /**
         * The key used to identify the filter parameter in the request.
         */
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
    };
    ```

2. Create a filter with `OnlyFor`:
    ```php
    use App\Models\Post;
    use App\Models\Comment;
    use Meius\LaravelFilter\Attributes\OnlyFor;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will be applied to the "Post" model and beyond only.
    return new #[OnlyFor(Post::class, Comment::class, ...)] class extends Filter
    {
        /**
         * The key used to identify the filter parameter in the request.
         */
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
    };
    ```

### Example Request Structure

1. For filters to work correctly, the query must have the appropriate structure. Here is an example of how the query should be structured:
    ```json
    {
      "filter": {
        "posts": {
          "title": "Deep Thoughts on the Hitchhiker's Guide",
          "published_after": "2005-04-28"
        },
        "comments": {
          "content": "The answer to this question is 42"
        }
      }
    }
    ```

2. Example request:
    ```http
    GET /posts?filter[posts][title]=Hitchhiker&filter[posts][published_after]=2005-04-28&filter[comments][content]=42
    ```

## Advanced Usage

### Complex Filters

1. Create a complex filter that combines multiple conditions:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        protected string $key = 'complex_filter';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('status', 'active')
                           ->where(function ($query) use ($value) {
                               $query->where('name', 'like', "%{$value}%")
                                     ->orWhere('description', 'like', "%{$value}%");
                           });
        }
    };
    ```

### Combined Filters

1. Apply multiple filters to a single query:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        protected string $key = 'combined_filter';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('category', $value['category'])
                           ->where('price', '>=', $value['min_price'])
                           ->where('price', '<=', $value['max_price']);
        }
    };
    ```

## Examples for Other Databases

Using the `query` method, you can create filters for different databases.

### PostgreSQL

1. Create a filter for a PostgreSQL database:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        protected string $key = 'title';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->whereRaw('title ILIKE ?', ["%{$value}%"]);
        }
    };
    ```

### SQLite

1. Create a filter for an SQLite database:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    return new class extends Filter
    {
        protected string $key = 'title';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('title', 'like', "%{$value}%");
        }
    };
    ```

## Extending Logic

### Adding Filter Directories

1. Create a custom service provider by extending `FilterServiceProvider`:
    ```php
    <?php

    namespace App\Providers;

    use Meius\LaravelFilter\Providers\FilterServiceProvider;

    class CustomFilterServiceProvider extends FilterServiceProvider
    {
        protected function discoverFiltersWithin(): array
        {
            return [
                app_path('path'),
            ];
        }
    }
    ```

2. Register the custom service provider in `config/app.php`:
    ```php
    'providers' => [
        // Other service providers
        App\Providers\CustomFilterServiceProvider::class,
    ],
    ```

## Support

For support, please open an issue on the [GitHub repository](https://github.com/brann-meius/laravel-filter/issues).

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
