<?php


namespace Eslym\BladeUtils\Tools;


use Generator;
use Illuminate\Support\Arr;

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

    public function isArray(): bool{
        $first = Arr::first($this->tokens);
        $seconds = count($this->tokens) > 1 ? $this->tokens[1] : null;
        $last = Arr::last($this->tokens);
        return ($first == '[' && $last == ']') ||
            (is_array($first) && $first[0] == T_ARRAY && $seconds == '(' && $last == ')');
    }

    /**
     * @return Argument[]|Generator
     */
    public function loopArray(){
        if(!$this->isArray()){
            return new Generator();
        }
        $tokens = (array)((object)$this->tokens);
        array_pop($tokens);
        $t = array_shift($tokens);
        if($t !== '['){
            array_shift($tokens);
        }
        $args = new Arguments(null, $tokens);
        foreach ($args as $arg){
            $tokens = (array)((object)$arg->tokens);
            $split = -1;
            foreach ($tokens as $i => $token){
                if(is_array($token) && $token[0] == T_DOUBLE_ARROW){
                    $split = $i;
                    break;
                }
            }
            if($split >= 0){
                $key = array_splice($tokens, 0, $split);
                array_shift($tokens);
                yield (new Arguments(null, $key))[0] => (new Arguments(null, $tokens))[0];
            } else {
                yield new Argument($arg->tokens);
            }
        }
    }

    public function tokens(){
        return $this->tokens;
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