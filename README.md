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
    'default' => env('GW_AUTH_DEFAULT_GATEWAY', 'default'),

    // 可配置后台接口网关、前台接口网关、openapi接口网关等
    'gateways' => [
        'default' => [
            'private-request' => [
                'app' => env('GW_AUTH_PRIVATE_APP'),
                'ticket' => env('GW_AUTH_PRIVATE_TICKET'),
                'ignore-check' => env('APP_DEBUG', false),  // 是否忽略校验，缺省是false
            ],
        ],

    ],
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

##### 3.1 示例一
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

##### 3.2 示例二，使用带有服务发现的私密请求
###### 先配置 `config/gw-auth.php`
```php
return [
    'default' => env('GW_AUTH_DEFAULT_GATEWAY', 'default'),

    'gateways' => [
        'default' => [
            // ...

            // 服务发现配置
            'service-discovery' => [
                'baseUrl' => env('GW_AUTH_SERVICE_DISCOVERY_BASE_URL'),
                'app' => env('GW_AUTH_SERVICE_DISCOVERY_APP'),
                'ticket' => env('GW_AUTH_SERVICE_DISCOVERY_TICKET'),
            ],
        ],

    ],
];
```

###### 请求示例：
```php
use Oh86\GW\Auth\HttpClient\PrivateRequestWithServiceDiscovery;

$req = new PrivateRequestWithServiceDiscovery('app1');

$response = $req->get('api/private/test', ['foo' => 'bar']);

$status = $response->status();
$arr = $response->json();
```