# Laravel Blade Utlities
A blade utilities including fix for ```@json``` directive and improvement on ```@each``` directive

## Installation
````bash
composer require eslym/laravel-blade-utils ^1.0
````

## Configuration
### Laravel
Let package discovery to do its job.

### Lumen
````php
# bootstrap/app.php
...
$app->register(Eslym\BladeUtils\Providers\BladeUtilServiceProvider::class);
...
````