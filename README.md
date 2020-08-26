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

## Config
By default it will generate Repository file in ```Repositories``` directory and
Interface in ```Interfaces``` directory.

You can change it in file ```config/laravel-common-command```

## Options

If you want to create something like ```EloquentRepository``` you can add prefix option to the command

Something like this

```
php artisan make:repository Product --prefix=Eloquent
```

You can also create Repository with template

```
php artisan make:repository Product --template
```

```php
namespace App\Repositories;

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

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->product->find($id);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id)
    {
        return $this->product->findOrFail($id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->product->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id)
    {
        $product = $this->findOrFail($id);
        return $product->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $product = $this->findOrFail($id);
        return $product->delete();
    }

}
```

```php
interface ProductRepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $id
     * @return mixed
     */
    public function findOrFail($id);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * @param array $data
     * @param $id
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id);
}
```
