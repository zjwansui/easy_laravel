#### 使用之前

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
