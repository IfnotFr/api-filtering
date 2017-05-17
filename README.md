# Api Filtering

## Installation

Require the package using composer :

    composer require ifnot/api-filtering

### Server-side only

Add the service provider into your `config/app.php` :

    Ifnot\ApiFiltering\Providers\ApiFilteringServiceProdiver::class,

Publish the configuration if you want to configure the default values :

    php artisan vendor:publish --provider="Ifnot\ApiFiltering\Providers\ApiFilteringServiceProdiver"

## Usage (server-side)

Add the filter ability to your eloquent models using the `Ifnot\ApiFiltering\Eloquent\Traits\CanBeFiltered` trait.

This is an example :

```php
namespace App;

use Ifnot\ApiFiltering\Eloquent\Traits\CanBeFiltered;

class MyModel extends Model
{
	use CanBeFiltered;
}
```

Use the scope in your controller when you are accessing to your model:

```php
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function index(Request $request)
    {
        return MyModel::filter($request->all)->get();
    }
}
```