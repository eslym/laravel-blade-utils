<?php


namespace Eslym\BladeUtils\Facades;


use Eslym\BladeUtils\Tools\Arguments;
use Illuminate\Support\Facades\Facade;

/**
 * Class BladeUtils
 * @package Eslym\BladeUtils\Facades
 *
 * @method static Arguments parseArguments(string $expression)
 * @method static string compileJson($expression)
 * @method static string compileCss($expression)
 * @method static string compileJs($expression)
 * @method static string compileImg($expression)
 * @method static string buildPropMeta(array $meta)
 * @method static string buildNameMeta(array $meta)
 * @method static string buildItemMeta(array $meta)
 */
class BladeUtils extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'blade-utils';
    }
}