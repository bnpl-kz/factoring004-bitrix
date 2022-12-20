# Factoring004 SDK

- [Requirements](#requirements)
- [Installation](#installation)
- [Order Manager](#order-manager)
- [Usage](#usage)
    * [Create api instance](#create-api-instance)
    * [Authentication](#authentication)
    * [Call endpoints](#call-endpoints)
    * [Error handling](#error-handling)
    * [Exception Hierarchy](#exception-hierarchy)
- [Advanced usage](#advanced-usage)
    * [PSR HTTP clients](#psr-http-clients)
    * [Transport layer](#transport-layer)
    * [Resources](#resources)

## Requirements

- PHP >=5.6
- JSON extension
- PSR-17, PSR-18 implementations (optional and PHP >=7.x only)

## Installation

```bash
composer require bnpl-partners/factoring004
```

## Order Manager

This class is high level api for order management

### Create an instance

```php
use BnplPartners\Factoring004\Order\OrderManager;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;

require_once __DIR__ . '/vendor/autoload.php';

$manager = OrderManager::create('http://api-domain.com', new BearerTokenAuth('Access Token'));
```

### PreApp endpoint

See [PreApp](#preapp)

```php
$response = $manager->preApp(...)
```

### Delivery endpoints

#### Send OTP

```php
$response = $manager->delivery($merchantId, $orderId, $amount)->sendOtp();
var_dump($response->getMessage());
```

#### Check OTP

```php
$response = $manager->delivery($merchantId, $orderId, $amount)->checkOtp($otp);
var_dump($response->getMessage());
```

#### Without OTP

```php
$response = $manager->delivery($merchantId, $orderId, $amount)->confirmWithoutOtp();
var_dump($response->getMessage());
```

### Full return endpoints

#### Send OTP

```php
$response = $manager->fullRefund($merchantId, $orderId)->sendOtp();
var_dump($response->getMessage());
```

#### Check OTP

```php
$response = $manager->fullRefund($merchantId, $orderId)->checkOtp($otp);
var_dump($response->getMessage());
```

#### Without OTP

```php
$response = $manager->fullRefund($merchantId, $orderId)->confirmWithoutOtp();
var_dump($response->getMessage());
```

### Partial return endpoints

#### Send OTP

```php
$response = $manager->partialRefund($merchantId, $orderId, $amount)->sendOtp();
var_dump($response->getMessage());
```

#### Check OTP

```php
$response = $manager->partialRefund($merchantId, $orderId, $amount)->checkOtp($otp);
var_dump($response->getMessage());
```

#### Without OTP

```php
$response = $manager->partialRefund($merchantId, $orderId, $amount)->confirmWithoutOtp();
var_dump($response->getMessage());
```

### Cancel endpoint

```php
$response = $manager->cancel($merchantId, $orderId);
var_dump($response->getMessage());
```

### Error handling

All methods can throw an exception like [there](#error-handling)

```php
use BnplPartners\Factoring004\Exception\PackageException;

try {
    $response = $manager->delivery($merchantId, $orderId, $amount)->sendOtp();
    var_dump($response->getMessage());
} catch (PackageException $e) {
    var_dump($e);
}
```

## Usage

### Create api instance

```php
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;

require_once __DIR__ . '/vendor/autoload.php';

$api = Api::create('http://api-domain.com', new BearerTokenAuth('Access Token'));
```

### Authentication

#### Generate access token

```php
use BnplPartners\Factoring004\Api;
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;

$tokenManager = new OAuthTokenManager($transport, 'http://api-domain.com', 'consumer key', 'consumer secret');
$token = $tokenManager->getAccessToken();

$api = Api::create($transport, 'http://api-domain.com', new BearerTokenAuth($token->getAccessToken()));
```

#### Cache access token

```php
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\OAuth\CacheOAuthTokenManager;
use BnplPartners\Factoring004\OAuth\OAuthTokenManager;

$tokenManager = new OAuthTokenManager($transport, 'http://api-domain.com', 'consumer key', 'consumer secret');

$cache = ... // PSR-16 Cache
$tokenManager = new CacheOAuthTokenManager($tokenManager, $cache, 'cache key');
```

### Call endpoints

#### PreApp

Create preApp

```php
use BnplPartners\Factoring004\PreApp\Item;
use BnplPartners\Factoring004\PreApp\PreAppMessage;
use BnplPartners\Factoring004\PreApp\PartnerData;

$message = new PreAppMessage(
    new PartnerData('test', 'test', 'test', 'test@example.com', 'http://example.com'),
    '1',
    6000,
    1,
    'http://your-store.com/success',
    'http://your-store.com/internal',
    [new Item('1', 'test', '1', 1, 6000, 8000)],
);

// Or
$message = PreAppMessage::createFromArray([
    'partnerData' => [
        'partnerName' => 'test',
        'partnerCode' => 'test',
        'pointCode' => 'test',
        'partnerEmail' => 'test@example.com',
        'partnerWebsite' => 'http://example.com',
    ],
    'billNumber' => '1',
    'billAmount' => 6000,
    'itemsQuantity' => 1,
    'successRedirect' => 'http://your-store.com/success',
    'postLink' => 'http://your-store.com/internal',
    'items' => [
        [
            'itemId' => '1',
            'itemName' => 'test',
            'itemCategory' => '1',
            'itemQuantity' => 1,
            'itemPrice' => 6000,
            'itemSum' => 8000,
        ],
    ],
]);

//Send request and receive response
$response = $api->preApps->preApp($message);

var_dump($response->getStatus(), $response->getPreAppId(), $response->getRedirectLink());
var_dump($response->toArray(), json_encode($response));
```

#### OTP

Send OTP

```php
use BnplPartners\Factoring004\Otp\SendOtp;

$sendOtp = new SendOtp('1', '1', 6000);

// or
$sendOtp = SendOtp::createFromArray(['merchantId' => '1', 'merchantOrderId' => '1', 'amount' => 6000]);

// send request and receive response
$response = $api->otp->sendOtp($sendOtp);

var_dump($response->getMsg());
var_dump($response->toArray(), json_encode($response));
```

Check OTP

```php
use BnplPartners\Factoring004\Otp\CheckOtp;

$checkOtp = new CheckOtp('1', '1', '1111', 6000);

// or
$checkOtp = CheckOtp::createFromArray(['merchantId' => '1', 'merchantOrderId' => '1', 'otp' => '1111', 'amount' => 6000]);

// send request and receive response
$response = $api->otp->checkOtp($checkOtp);

var_dump($response->getMsg());
var_dump($response->toArray(), json_encode($response));
```

Send OTP Return

```php
use BnplPartners\Factoring004\Otp\SendOtpReturn;

$sendOtpReturn = new SendOtpReturn(6000, '1', '1');

// or
$sendOtpReturn = SendOtpReturn::createFromArray(['amountAr' => 6000, 'merchantId' => '1', 'merchantOrderId' => '1']);

// send request and receive response
$response = $api->otp->sendOtpReturn($sendOtpReturn);

var_dump($response->getMsg());
var_dump($response->toArray(), json_encode($response));
```

Check OTP Return

```php
use BnplPartners\Factoring004\Otp\CheckOtpReturn;

$checkOtpReturn = new CheckOtpReturn(6000, '1', '1', '1111');

// or
$checkOtpReturn = CheckOtp::createFromArray(['amountAr' => 6000, 'merchantId' => '1', 'merchantOrderId' => '1', 'otp' => '1111']);

// send request and receive response
$response = $api->otp->checkOtpReturn($checkOtpReturn);

var_dump($response->getMsg());
var_dump($response->toArray(), json_encode($response));
```

#### Delivery & Return

Delivery

```php
use BnplPartners\Factoring004\ChangeStatus\DeliveryOrder;
use BnplPartners\Factoring004\ChangeStatus\DeliveryStatus;
use BnplPartners\Factoring004\ChangeStatus\ErrorResponse;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\SuccessResponse;

$orders = new MerchantsOrders('1', [new DeliveryOrder('1', DeliveryStatus::DELIVERY(), 6000)]);

// or
$orders = MerchantsOrders::createFromArray([
    'merchantId' => '1',
    'orders' => [
        ['orderId' => '1', 'status' => 'delivery', 'amount' => 6000],
    ],
]);

// send request and receive response
$response = $api->changeStatus->changeStatusJson($orders);

var_dump(array_map(fn(SuccessResponse $response) => $response->getMsg(), $response->getSuccessfulResponses()));
var_dump(array_map(fn(ErrorResponse $response) => $response->getMessage(), $response->getErrorResponses()));
var_dump($response->toArray(), json_encode($response));
```

Return

```php
use BnplPartners\Factoring004\ChangeStatus\ErrorResponse;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\ReturnOrder;
use BnplPartners\Factoring004\ChangeStatus\ReturnStatus;
use BnplPartners\Factoring004\ChangeStatus\SuccessResponse;

$orders = new MerchantsOrders('1', [new ReturnOrder('1', ReturnStatus::RETURN(), 6000)]);

// or
$orders = MerchantsOrders::createFromArray([
    'merchantId' => '1',
    'orders' => [
        ['orderId' => '1', 'status' => 'return', 'amount' => 6000],
    ],
]);

// send request and receive response
$response = $api->changeStatus->changeStatusJson($orders);

var_dump(array_map(fn(SuccessResponse $response) => $response->getMsg(), $response->getSuccessfulResponses()));
var_dump(array_map(fn(ErrorResponse $response) => $response->getMessage(), $response->getErrorResponses()));
var_dump($response->toArray(), json_encode($response));
```

Cancel

```php
use BnplPartners\Factoring004\ChangeStatus\ErrorResponse;
use BnplPartners\Factoring004\ChangeStatus\MerchantsOrders;
use BnplPartners\Factoring004\ChangeStatus\CancelOrder;
use BnplPartners\Factoring004\ChangeStatus\CancelStatus;
use BnplPartners\Factoring004\ChangeStatus\SuccessResponse;

$orders = new MerchantsOrders('1', [new CancelOrder('1', CancelStatus::CANCEL())]);

// or
$orders = MerchantsOrders::createFromArray([
    'merchantId' => '1',
    'orders' => [
        ['orderId' => '1', 'status' => 'canceled'],
    ],
]);

// send request and receive response
$response = $api->changeStatus->changeStatusJson($orders);

var_dump(array_map(fn(SuccessResponse $response) => $response->getMsg(), $response->getSuccessfulResponses()));
var_dump(array_map(fn(ErrorResponse $response) => $response->getMessage(), $response->getErrorResponses()));
var_dump($response->toArray(), json_encode($response));


```


### Error handling

Whenever api returns an error client will throw an instance of ``BnplPartners\Factoring004\Exception\ApiException``.

```php
use BnplPartners\Factoring004\Exception\ApiException;
use BnplPartners\Factoring004\Exception\AuthenticationException;
use BnplPartners\Factoring004\Exception\EndpointUnavailableException;
use BnplPartners\Factoring004\Exception\ErrorResponseException;
use BnplPartners\Factoring004\Exception\NetworkException;
use BnplPartners\Factoring004\Exception\TransportException;
use BnplPartners\Factoring004\Exception\UnexpectedResponseException;
use BnplPartners\Factoring004\Exception\ValidationException;

try {
    $api->preApps->preApp($message);
} catch (AuthenticationException $e) {
    // authentication errors, token invalid, token expired, etc
    var_dump($e->getCode(), $e->getMessage(), $e->getDescription());
} catch (EndpointUnavailableException $e) {
    // to catch all internal server errors 500, 503, etc
    var_dump($e->getResponse());
} catch (ErrorResponseException $e) {
    // to catch all client errors 400, 405, etc
    var_dump($e->getErrorResponse());
} catch (UnexpectedResponseException $e) {
    // to catch all responses with unexpected schema
    var_dump($e->getResponse());
} catch (ValidationException $e) {
    // endpoint validation error
    $details = $e->getResponse()->getDetails();
    var_dump($details[0]->getError(), $details[0]->getField());
} catch (ApiException $e) {
    // to catch all api layer exceptions
} catch (NetworkException $e) {
    // network issues, connection refused, etc
} catch (TransportException $e) {
    // to catch all transport layer exceptions
}
```

## Exception Hierarchy

- ``PackageException``
  * ``ApiException``
    * ``AuthenticationException``
    * ``ValidationException``
    * ``ErrorResponseException``
    * ``UnexpectedResponseException``
      * ``EndpointUnavailableException``
  * ``TransportException``
    * ``NetworkException``
    * ``DataSerializationException``

## Advanced usage

### PSR HTTP clients

First install any PSR-17 and PSR-18 packages.
See [PSR-17 packages](https://packagist.org/providers/psr/http-factory-implementation) and
[PSR-18 packages](https://packagist.org/providers/psr/http-client-implementation).

For instance, we install popular Guzzle HTTP client. Since version 7 it has already implemented PSR-17 and PSR-18.

```bash
composer require guzzlehttp/guzzle
```

For Guzzle 6 you may use ``GuzzleTransport`` or ``PsrTransport`` with additional PSR-17 and PSR-18 adapters. See bellow.

Install PSR-17 and PSR-18 adapters for Guzzle 6.

```bash
composer require http-interop/http-factory-guzzle mjelamanov/psr18-guzzle
```

### Transport layer

Transport is an abstraction layer over HTTP clients.

For Guzzle 6 and 7

```php
use BnplPartners\Factoring004\Transport\GuzzleTransport;

require_once __DIR__ . '/vendor/autoload.php';

$transport = new GuzzleTransport(new GuzzleHttp\Client());
```

For PSR-17 and PSR-18 client.

```php
use BnplPartners\Factoring004\Transport\PsrTransport;

require_once __DIR__ . '/vendor/autoload.php';

$transport = new PsrTransport(
    new GuzzleHttp\Psr7\HttpFactory(),
    new GuzzleHttp\Psr7\HttpFactory(),
    new GuzzleHttp\Psr7\HttpFactory(),
    new GuzzleHttp\Client(),
);
```

For Guzzle 6 client with PSR-17 and PSR-18 adapters.

```php
use BnplPartners\Factoring004\Transport\PsrTransport;

require_once __DIR__ . '/vendor/autoload.php';

$transport = new PsrTransport(
    new Http\Factory\Guzzle\RequestFactory(),
    new Http\Factory\Guzzle\StreamFactory(),
    new Http\Factory\Guzzle\UriFactory(),
    new Mjelamanov\GuzzlePsr18\Client(new GuzzleHttp\Client()),
);
```

For other PSR-17 and PSR-18 clients.

```php
use BnplPartners\Factoring004\Transport\PsrTransport;

require_once __DIR__ . '/vendor/autoload.php';

$transport = new PsrTransport(
    new RequestFactory(), // PSR-17 instance
    new StreamFactory(), // PSR-17 instance
    new UriFactory(), // PSR-17 instance
    new Client() // PSR-18 instance
);
```

You can create your own transport. Just implement ``BnplPartners\Factoring004\Transport\TransportInterface``.

#### Send requests

```php
$response = $transport->post('/bnpl-partners/1.0/preapp', ['partnerData' => [...]], ['Content-Type' => 'application/json']);

var_dump($response->getStatusCode()); // HTTP response status code
var_dump($response->getHeaders()); // HTTP response headers
var_dump($response->getBody()); // parsed HTTP response body
```

### Resources

Each resource is a set of grouped endpoints.

```php
use BnplPartners\Factoring004\Auth\BearerTokenAuth;
use BnplPartners\Factoring004\Otp\OtpResource;
use BnplPartners\Factoring004\PreApp\PreAppResource;

require_once __DIR__ . '/vendor/autoload.php';

...

$preApp = new PreAppResource($transport, 'http://api-domain.com', new BearerTokenAuth('Access Token'));
$response = $preApp->preApp(...);

$otp = new OtpResource($transport, 'http://api-domain.com', new BearerTokenAuth('Access Token'));
$response = $otp->sendOtp(...);
```

## Test

```bash
./vendor/bin/phpunit
```