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
This fix made ```@json``` directive supports more complex syntax.

```@json([$val1, $val2, $val3, $val4])``` will be compiled to ```<?php echo json_encode([$val1, $val2, $val3, $val4], 15, 512); ?>``` instead of ```<?php echo json_encode([$val1, $val2, $val3); ?>``` by original blade directive.

#### Better ```@each``` directive
```@each``` directive will now include with variables in current scope.

#### ```@js``` and ```@css``` directives
Syntax:
````blade
@js($uri, [$sri], [$crossorigin])
@css($uri, [$sri], [$crossorigin])
````

### PhpStorm
If you are using phpstorm, please enable custom blade settings and add these entries into ```.idea/blade.xml``` for type hint.
````xml
<directives>
    ...
    <data directive="@js" injection="true" prefix="&lt;?php __ide_blade_helper::js(" suffix="); ?>"/>
    <data directive="@css" injection="true" prefix="&lt;?php __ide_blade_helper::css(" suffix="); ?>"/>
    <data directive="@img" injection="true" prefix="&lt;?php __ide_blade_helper::img(" suffix="); ?>"/>
    <data directive="@iif" injection="true" prefix="&lt;?php __ide_blade_helper::iif(" suffix="); ?>"/>
    <data directive="@meta" injection="true" prefix="&lt;?php __ide_blade_helper::meta(" suffix="); ?>"/>
    <data directive="@nameMeta" injection="true" prefix="&lt;?php __ide_blade_helper::nameMeta(" suffix="); ?>"/>
    <data directive="@propMeta" injection="true" prefix="&lt;?php __ide_blade_helper::propMeta(" suffix="); ?>"/>
    <data directive="@itemMeta" injection="true" prefix="&lt;?php __ide_blade_helper::itemMeta(" suffix="); ?>"/> 
</directives>
````