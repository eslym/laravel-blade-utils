<?php


namespace Eslym\BladeUtils\Tools;


class BladeUtils
{
    public function parseArguments(string $expressions): Arguments{
        return new Arguments($expressions);
    }

    public function buildPropMeta(array $meta): string{
        $result = '';
        foreach ($meta as $key => $value){
            $result.= '<meta property="'.e($key).'" content="'.e($value).'" >';
        }
        return $result;
    }

    public function buildNameMeta(array $meta): string{
        $result = '';
        foreach ($meta as $key => $value){
            $result.= '<meta name="'.e($key).'" content="'.e($value).'" >';
        }
        return $result;
    }

    public function buildItemMeta(array $meta): string{
        $result = '';
        foreach ($meta as $key => $value){
            $result.= '<meta itemprop="'.e($key).'" content="'.e($value).'" >';
        }
        return $result;
    }
}