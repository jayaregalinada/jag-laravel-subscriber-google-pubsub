# Laravel Subscriber (Google PubSub)
Laravel Subscriber using [Google PubSub](https://cloud.google.com/pubsub/)
> __⚠️ NOTE: Currently on development, this may drastically change without further notice__

### Requirements
- PHP `^7.1`
- Laravel/Lumen `^7.0`
- [gRPC](https://cloud.google.com/php/grpc) (Optional but increase performance)

### Getting Started

##### Install composer
```sh
composer require jag/laravel-subscriber-google-pubsub
```

##### Add Service Provider
Since Laravel 5.5 [Auto Discovery](https://medium.com/@taylorotwell/package-auto-discovery-in-laravel-5-5-ea9e3ab20518) is enabled by default, but in case you disable, on your `config/app.php`
```php 
...
'providers' => [
    ...
    Jag\Subscriber\GooglePubSub\Providers\LaravelServiceProvider::class,
]
...
```
if Lumen however, on your `bootstrap/app.php`
```php
... 
$app->register(Jag\Subscriber\GooglePubSub\Providers\LaravelServiceProvider::class);
...
```

### Configuration

| Key                | Description                             | Type    | Default                                                                                     |
|--------------------|-----------------------------------------|---------|------------------|
| project_id         | PubSub Project ID                       | String  | null             |
| credentials_path   | Path for credentials                    | String  | storage/key.json |
| max_messages       | Maximum message can pulled              | Integer | 1000             |
| return_immediately | Return response immediate (DEPRECATED)  | Boolean | false            |
| override_config    | Override configuration except projectId | Array   | []               |

> __More documentations will be added in the Wiki__
* * *
###### Created and Developed by [Jay Are Galinada](https://jayaregalinada.github.io)
