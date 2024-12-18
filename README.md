# Laravel Filter Package

[![Build Status](https://img.shields.io/github/actions/workflow/status/brann-meius/laravel-filter/ci.yml)](https://github.com/brann-meius/laravel-filter/actions)
[![codecov](https://codecov.io/github/brann-meius/laravel-filter/graph/badge.svg?token=36QKLEBBTV)](https://codecov.io/github/brann-meius/laravel-filter)
[![License](https://img.shields.io/github/license/brann-meius/laravel-filter)](LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D%208.0-blue)](https://www.php.net/)
[![Laravel Version](https://img.shields.io/badge/laravel-%3E%3D%209.0-orange)](https://laravel.com/)

## Table of Contents

- [Overview](#overview)
- [Requirements](#requirements)
- [Getting Started](#getting-started)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Creating Filters](#creating-filters)
    - [Applying Filters](#applying-filters)
    - [Applying Filters to Route Groups](#applying-filters-to-route-groups)
    - [Caching Filters](#caching-filters)
    - [Example](#example)
    - [Using `ExcludeFrom` and `OnlyFor` Attributes](#using-excludefrom-and-onlyfor-attributes)
    - [Prioritization of Settings](#prioritization-of-settings)
    - [Example Request Structure](#example-request-structure)
    - [Advanced Usage](#advanced-usage)
- [Configuring Filter Aliases and Prefixes](#configuring-filter-aliases-and-prefixes)
    - [Adding Filter Aliases to Models](#adding-filter-aliases-to-models)
    - [Configuring the Filter Prefix](#configuring-the-filter-prefix)
    - [Example JSON and URL Requests](#example-json-and-url-requests)
- [Examples for Other Databases](#examples-for-other-databases)
- [Support](#support)
- [License](#license)

## Overview

The `meius/laravel-filter` package provides a convenient way to apply filters to Eloquent models in a Laravel application. It allows you to define filters using attributes and apply them dynamically based on the request.

## Requirements

- PHP \>= 8.0
- Laravel \>= 9.0

## Getting Started

To get started with the `meius/laravel-filter` package, follow the installation instructions below and check out the usage examples.

## Installation

1. Install the package via Composer:
    ```bash
    composer require meius/laravel-filter
    ```

## Configuration

1. To publish the configuration file, run the following command:
    ```bash
    php artisan vendor:publish --tag=filter-config
    ```

This will create a `config/filter.php` file where you can customize the filter settings.

## Usage

### Creating Filters

1. To create a new filter, use the `make:filter` Artisan command:
    ```bash
    php artisan make:filter {name}
    ```

### Applying Filters

1. Define filters using attributes in your controller methods:
    ```php
    use App\Attributes\Filter\ApplyFiltersTo;
    use App\Models\Post;

    class PostController
    {
        #[ApplyFiltersTo(Post::class)]
        public function index()
        {
            return Post::query()->get();
        }
    }
    ```

2. To apply filters to related models, use the `ApplyFiltersTo` attribute with multiple model classes:
    ```php
    use App\Attributes\Filter\ApplyFiltersTo;
    use App\Models\Author;
    use App\Models\Comment;
    use App\Models\Post;

    class PostController
    {
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

### Applying Filters to Route Groups

By default, filters are applied to the web and API route groups. To customize this behavior, you must publish the
configuration file and make the necessary adjustments.

1. **Publish the Configuration File**:
   ```bash
   php artisan vendor:publish --tag=filter-config
   ```

2. **Update the Configuration**:
   Open the `config/filter.php` file and modify the `route_groups` section to specify which groups should have filters
   applied.

    ```php
    return [
        // Filters will not be applied to API route groups with this change.
        'route_groups' => [
            //'api',
            'web',
        ],
    ];
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

    class TitleFilter extends Filter
    {
        /**
         * The key used to identify the filter parameter in the request.
         */
        protected string $key = 'title';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('title', 'like', "%$value%");
        }
    }
    ```

2. Create a filter for related models:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    class AuthorIdFilter extends Filter
    {
        protected string $key = 'author_id';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->whereHas('author', function (Builder $query) use ($value): void {
                $query->where('id', '=', $value);
            });
        }
    }
    ```

3. If you need to apply a filter according to a condition, you can use the `canContinue` method:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    class OwnerFilter extends Filter
    {
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

### Using `ExcludeFrom` and `OnlyFor` Attributes

You can use the `ExcludeFrom` and `OnlyFor` attributes to conditionally apply filters.

1. Create a filter with `ExcludeFrom`:
    ```php
    use App\Models\User;
    use App\Models\Category;
    use Meius\LaravelFilter\Attributes\ExcludeFrom;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will never be applied to the "User" model and beyond.
    #[ExcludeFrom(User::class, Category::class, ...)]
    class ContentFilter extends Filter
    {
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
    }
    ```

2. Create a filter with `OnlyFor`:
    ```php
    use App\Models\Post;
    use App\Models\Comment;
    use Meius\LaravelFilter\Attributes\OnlyFor;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will be applied to the "Post" model and beyond only.
    #[OnlyFor(Post::class, Comment::class, ...)] 
    class ContentFilter extends Filter
    {
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
    }
    ```

3. You can also use the `ExcludeFrom` and `OnlyFor` attributes together(If you need it \\@_@/):
    ```php
    use App\Models\Comment;
    use App\Models\Post;
    use App\Models\User;
    use Meius\LaravelFilter\Attributes\ExcludeFrom;
    use Meius\LaravelFilter\Attributes\OnlyFor;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will be applied to the "Comment" and "User" models only.
    #[
        OnlyFor(Post::class, Comment::class, User::class),
        ExcludeFrom(Post::class),
    ] 
    class ContentFilter extends Filter
    {
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
    }
    ```
   
4. Create a filter using properties:
    ```php
    use App\Models\Comment;
    use App\Models\Post;
    use Meius\LaravelFilter\Filters\Filter;

    class ContentFilter extends Filter
    {
        /**
         * The models to which the filter should exclusively apply.
         *
         * @var array<Model>
         */
        protected array $onlyFor = [
            Comment::class,
            Post::class,
        ];
        
        /**
         * The models to which the filter should not be applied.
         *
         * @var array<Model>
         */
        protected array $excludeFrom = [];
   
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
    }
    ```
   
5. Create a filter using methods:
    ```php
    use App\Models\Comment;
    use App\Models\Post;
    use Meius\LaravelFilter\Filters\Filter;

    class ContentFilter extends Filter
    {   
        protected string $key = 'content';

        protected function query(Builder $builder, $value): Builder
        {
            // Filter logic
        }
   
        protected function onlyFor(): array
        {
            return [
                User::class,
                Category::class,
            ];
        }
   
        protected function excludeFrom(): array
        {
            return [];
        }
    }
    ```

### Prioritization of Settings

When defining filter settings, the priority of the settings is as follows:

1. **Method**: The highest priority. If a method is defined to specify the filter settings, it will override any settings defined via properties or attributes.
2. **Property**: Medium priority. If a property is defined to specify the filter settings, it will override any settings defined via attributes but will be overridden by method definitions.
3. **Attribute**: The lowest priority. If settings are defined via attributes, they will be used only if there are no corresponding settings defined via methods or properties.

This means that if there are conflicting settings defined in multiple ways, only the setting with the highest priority will be applied. For example, if a filter has both a method and an attribute defining the same setting, the method's setting will take precedence and be applied, while the attribute's setting will be ignored.

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
    ```http request
    GET /posts?filter[posts][title]=Hitchhiker&filter[posts][published_after]=2005-04-28&filter[comments][content]=42
    ```
   
## Configuring Filter Aliases and Prefixes

### Adding Filter Aliases to Models

1. To add filter aliases to your models, use the `HasFilterAlias` trait and define the `$filterAlias` property in your model.

    ```php
    namespace App\Models;
    
    use Illuminate\Database\Eloquent\Model;
    use Meius\LaravelFilter\Traits\HasFilterAlias;
    
    class HtmlFivePackage extends Model
    {
        use HasFilterAlias;
    
        protected string $filterAlias = 'h5p';
    }
    ```

### Configuring the Filter Prefix

1. You can change the filter prefix by modifying the `config/filter.php` configuration file. Update the `prefix` value to your desired string.

    ```php
    return [
        'prefix' => 'f',
    ];
    ```

### Example JSON and URL Requests

1. JSON Request

   Here, `"f"` is the filter prefix specified in the config, and `"h5p"` is the alias defined in the model.

    ```json
    {
        "f": {
            "h5p": {
                "name": "blog"
            }
        }
    }
    ```

2. URL Request

   Similarly, in the URL request, `"f"` represents the filter prefix and `"h5p"` is the alias.

    ```http request
    GET /posts?f[h5p][name]=blog
    ```

## Advanced Usage

### Complex Filters

1. Create a complex filter that combines multiple conditions:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    class ComplexFilter extends Filter
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
    }
    ```

### Combined Filters

1. Apply multiple filters to a single query:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    class CombinedFilter extends Filter
    {
        protected string $key = 'combined_filter';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('category', $value['category'])
                ->where('price', '>=', $value['min_price'])
                ->where('price', '<=', $value['max_price']);
        }
    }
    ```

## Examples for Other Databases

Using the `query` method, you can create filters for different databases.

### PostgreSQL

1. Create a filter for a PostgreSQL database:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    class TitleFilter extends Filter
    {
        protected string $key = 'title';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->whereRaw('title ILIKE ?', ["%{$value}%"]);
        }
    }
    ```

### SQLite

1. Create a filter for an SQLite database:
    ```php
    use Illuminate\Database\Eloquent\Builder;
    use Meius\LaravelFilter\Filters\Filter;

    class TitleFilter extends Filter
    {
        protected string $key = 'title';

        protected function query(Builder $builder, $value): Builder
        {
            return $builder->where('title', 'like', "%{$value}%");
        }
    }
    ```

## Support

For support, please open an issue on the [GitHub repository](https://github.com/brann-meius/laravel-filter/issues).

### Contributing

We welcome contributions to the `meius/laravel-filter` library. To contribute, follow these steps:

1. **Fork the Repository**: Fork the repository on GitHub and clone it to your local machine.
2. **Create a Branch**: Create a new branch for your feature or bugfix.
3. **Write Tests**: Write tests to cover your changes.
4. **Run Tests**: Ensure all tests pass by running `phpunit`.
5. **Submit a Pull Request**: Submit a pull request with a clear description of your changes.

For more details, refer to the [CONTRIBUTING.md](CONTRIBUTING.md) file.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).
