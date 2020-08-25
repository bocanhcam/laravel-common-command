# Laravel Common Command

A Laravel Package help you to generate some common command like Repository...

## Install

```
composer require hora/laravel-common-command
```

```
php artisan vendor:publish --provider="Hora\LaravelCommonCommand\LaravelCommonCommandServiceProvider"
```

## Usage

Try command like this to generate Repository and Interface
```
php artisan make:repository Product
```
The generate files look like this : 

```php
namespace App\Repositories\Eloquents;

use App\Interfaces\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository implements ProductRepositoryInterface
{

    protected $product;

    /**
     * Product constructor.
     * @param Product $product
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
    }

}
```

```php
namespace App\Interfaces;

interface ProductRepositoryInterface
{

}
```

By default it will generate Repository file in ```Repositories``` directory and
Interface in ```Interfaces``` directory.

You can change it in file ```config/laravel-common-command```

If you want to create something like ```EloquentRepository``` you can add prefix option to the command

Something like this

```
php artisan make:repository Product --prefix=Eloquent
```
