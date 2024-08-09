# Laravel Filter Package

## Overview

The `meius/laravel-filter` package provides a convenient way to apply filters to Eloquent models in a Laravel application. It allows you to define filters using attributes and apply them dynamically based on the request.

## Installation

1. To install this private package, add the following repository to your `composer.json`:
    ```json
    {
      "repositories": [
        {
          "type": "github",
          "url": "https://github.com/brann-meius/laravel-filter.git"
        }
      ],
      "config": {
        "github-oauth": {
          "github.com": "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"
        }
      }
    }
    ```

2. Install the package via Composer:
    ```bash
    composer require meius/laravel-filter:dev-develop
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
2. Or extends the `Controller`:
    ```php
    use Meius\LaravelFilter\Http\Controllers\Controller;

    class PostController extends Controller
    {
        // Your methods
    }
    ```

3. Define filters using attributes in your controller methods:
    ```php
    use App\Attributes\Filter\ApplyFiltersTo;
    use App\Models\Post;

    class PostController
    {
        use Filterable;

        #[ApplyFiltersTo(Post::class, ...Models)]
        public function index()
        {
            // Your method logic
        }
    }
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
   }
   ```

2. If you need to apply a filter according to a condition, you can use the `canContinue` method:
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
           return $request->user()->verified();
       }
   }
   ```

### Using `ExcludeFor` and `OnlyFor` Attributes

You can use the `ExcludeFor` and `OnlyFor` attributes to conditionally apply filters.

1. Create a filter with `ExcludeFor`:
    ```php
    use App\Models\User;
    use Meius\LaravelFilter\Attributes\ExcludeFor;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will never be applied to the "User" model and beyond.
    return new #[ExcludeFor(User::class, ...Models)] class extends Filter
    {
       /**
        * The key used to identify the filter parameter in the request.
        */
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
    use Meius\LaravelFilter\Attributes\OnlyFor;
    use Meius\LaravelFilter\Filters\Filter;

    // The filter will be applied to the "Post" model and beyond only.
    return new #[OnlyFor(Post::class, ...Models)] class extends Filter
    {
       /**
        * The key used to identify the filter parameter in the request.
        */
       protected string $key = 'content';

       protected function query(Builder $builder, $value): Builder
       {
           // Filter logic
       }
    }
    ```

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).
