# oh86/laravel-gw网关鉴权组件库

### 一、生成配置文件
```shell
php artisan vendor:publish --provider='Oh86\GW\Auth\GatewayAuthServiceProvider'
```

### 二、简易教程

#### 1.私密请求校验中间件

##### 1.1 配置 `config/gw-auth.php`
```php
return [
    'private-requests' => [
        'admin' => [
            'app' => env('GW_AUTH_PRIVATE_APP'),
            'ticket' => env('GW_AUTH_PRIVATE_TICKET'),
        ],

        // ...
    ],
];
```

##### 1.2 使用中间件 `Oh86\GW\Auth\Middleware\CheckPrivateRequest`
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Oh86\GW\Auth\Middleware\CheckPrivateRequest;

Route::post('api/private/test', [TestController::class, 'test'])->middleware([
    CheckPrivateRequest::class . ':admin',
]);
```

#### 2.用户认证状态校验和获取

##### 2.1 配置 `config/auth.php`
```php
return [
    // ...
    'guards' => [
        // ...

        'gw-auth' => [
            'driver' => 'gw',
            'header' => 'Gw-Auth-Info',
            'user-class' => Oh86\GW\Auth\Guard\User::class,
        ]
    ],

    // ...
];
```

##### 2.2 校验用户登录状态和获取用户信息
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Oh86\GW\Auth\Middleware\CheckPrivateRequest;

Route::post('api/private/auth/test', function(Request $request) {
    $user = Auth::user();

    return $user;
})->middleware([
    CheckPrivateRequest::class . ':admin',
    'auth:gw-auth',
]);
```
