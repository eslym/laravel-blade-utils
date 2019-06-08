# Laravel Blade Utlities
Some blade utilities including fix for ```@json``` directive and improvement on ```@each``` directive

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

### Features
#### Fix for ```@json``` directive
This fix made json directive supports more complex syntax.

```@json([$val1, $val2, $val3, $val4])``` will be compiled to ```<?php echo json_encode([$val1, $val2, $val3, $val4], 15, 512); ?>``` instead of ```<?php echo json_encode([$val1, $val2, $val3); ?>``` by original blade directive.

#### Better ```@each``` directive
```@each``` directive will now include with variables in current scope.