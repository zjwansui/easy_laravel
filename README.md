#### 使用之前
结合laravel和swagger

`composer require darkaonline/l5-swagger`

`php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"`

```php
// controller.php
/**
 * @OA\Info (
 *     title="example-laravel-8.x",
 *     version="1.0"
 * )
 */
```

`php artisan l5-swagger:generate`



###### 使用response  request

`config/app.php`

```angular2html
providers数组中添加

\Zjwansui\EasyLaravel\Generator\CaptureServiceProvider::class,
\Illuminate\Foundation\Providers\ArtisanServiceProvider::class
```

###### 使用model

```php
 // ModelSaving：
// EventServiceProvider
protected $listen = [

ModelSaving::class => [
//SaveCreator::class, 看情况使用
CheckRules::class,
],]
```

###### 添加中间件

```php
//app/Http/Kernel.php
// $routeMiddleware 注册

  protected $routeMiddleware = [
        // ...
        'request'=>RegisterRequest::class
    ];

// route
Route::middleware('request')->get('/test',[TestController::class,'test']);

```
