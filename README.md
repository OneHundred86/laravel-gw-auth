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
    'private-request' => [
        'app' => env('GW_AUTH_PRIVATE_APP'),        // 必须
        'ticket' => env('GW_AUTH_PRIVATE_TICKET'),  // 必须
        'ignore-check' => env('APP_DEBUG', false),  // 是否忽略校验，非必须
    ],
    // ...
];
```

##### 1.2 使用中间件 `Oh86\GW\Auth\Middleware\CheckPrivateRequest`
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Oh86\GW\Auth\Middleware\CheckPrivateRequest;

Route::post('api/private/test', [TestController::class, 'test'])->middleware([
    CheckPrivateRequest::class,
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
            'header' => 'GW-Auth-Info',
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
    CheckPrivateRequest::class,
    'auth:gw-auth',
]);
```

#### 3.发送私密请求
```php
use Oh86\GW\Auth\HttpClient\PrivateRequest;

$req = new PrivateRequest([
    'baseUrl' => 'http://127.0.0.1:8000', 
    'app' => 'app1', 
    'ticket' => '...',
    ]);

$response = $req->get('api/private/test', ['foo' => 'bar']);

$status = $response->status();
$arr = $response->json();
```

#### 4.校验权限编码
##### 4.1 配置 `config/gw-auth.php`
```php
return [
    // ...

    // 配置权限编码的请求头
    'permission-codes-header' => env('GW_AUTH_PERMISSION_CODES_HEADER', 'GW-Permission-Codes'),

    // ...
];
```

##### 4.2 使用中间件 `Oh86\GW\Auth\Middleware\CheckPermissionCode`
```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Oh86\GW\Auth\Middleware\CheckPrivateRequest;
use Oh86\GW\Auth\Middleware\CheckPermissionCode;

Route::post('api/private/auth/test', function(Request $request) {
    $user = Auth::user();

    return $user;
})->middleware([
    CheckPrivateRequest::class,
    'auth:gw-auth',
    CheckPermissionCode::class . ':add-post',
]);
```