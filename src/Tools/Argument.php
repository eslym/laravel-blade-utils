<?php


namespace Eslym\BladeUtils\Tools;


class Argument
{
    private $tokens;

    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    public function __toString()
    {
        return (string)array_reduce($this->tokens, function($arg, $token){
            return $arg.(is_string($token) ? $token : $token[1]);
        }, '');
    }

    public function isSimple(){
        $tokens = $this->tokens;
        if(
            count($tokens) == 1 &&
            is_array($token = $tokens[0]) &&
            (
                (in_array($token[0], [T_LNUMBER, T_DNUMBER, T_CONSTANT_ENCAPSED_STRING])) ||
                ($token[0] == T_STRING && in_array(strtolower($token[1]), ['true', 'false', 'null']))
            )
        ){
            return true;
        }
        return false;
    }

    public function val(){
        if($this->isSimple()){
            return eval('return ('.$this->toString().');');
        }
        return null;
    }

    public function toString(){
        return $this->__toString();
    }
}