<?php


namespace Eslym\BladeUtils\Tools;


use ArrayAccess;
use Countable;
use Exception;
use IteratorAggregate;
use Traversable;

class Arguments implements ArrayAccess, Countable, IteratorAggregate
{
    const TYPE_CONSTANT = 1;
    const TYPE_DYNAMIC = 2;

    const TOKEN_CLOSE_MAP = [
        "`" => "`",
        "(" => ")",
        "{" => "}",
        "[" => "]",
        '"' => '"',
        T_CURLY_OPEN => "}",
        T_DOLLAR_OPEN_CURLY_BRACES => "}",
    ];

    /**
     * @var Argument[]
     */
    private $arguments = [];

    public function __construct($expression, $tokens = null)
    {
        if($tokens == null){
            $tokens = token_get_all('<?php '.$expression);
        }
        $p_stack = [];
        $arguments = [[]];
        array_shift($tokens);
        while($token = array_shift($tokens)){
            if(is_string($token)){
                if($token == ',' && empty($p_stack)){
                    array_unshift($arguments, []);
                    continue;
                }
                $arguments[0][]=$token;
                $last = end($p_stack);
                if(isset(self::TOKEN_CLOSE_MAP[$last]) && self::TOKEN_CLOSE_MAP[$last] == $token){
                    array_pop($p_stack);
                } else if(isset(self::TOKEN_CLOSE_MAP[$token])){
                    $p_stack[]= $token;
                }
            } else {
                $arguments[0][]=$token;
                if(isset(self::TOKEN_CLOSE_MAP[$token[0]])){
                    $p_stack[]= $token[0];
                }
            }
        }
        $this->arguments = array_reverse(array_map(function($tokens){
            return new Argument($this->trim($tokens));
        }, $arguments));
    }

    private function trim($tokens){
        do{
            $trim = false;
            if(is_array($tokens[0]) && $tokens[0][0] == T_WHITESPACE){
                $trim = true;
                array_shift($tokens);
            }
            if(is_array(end($tokens)) && end($tokens)[0] == T_WHITESPACE){
                $trim = true;
                array_pop($tokens);
            }
            if($tokens[0] == '(' && ($end = array_pop($tokens)) == ')'){
                $p_stack = ['('];
                $parentheses = true;
                foreach($tokens as $token){
                    if(is_string($token)){
                        $last = end($p_stack);
                        if(isset(self::TOKEN_CLOSE_MAP[$last]) && self::TOKEN_CLOSE_MAP[$last] == $token){
                            array_pop($p_stack);
                            if(empty($p_stack)){
                                $parentheses = false;
                                break;
                            }
                        } else if(isset(self::TOKEN_CLOSE_MAP[$token])){
                            $p_stack[]= $token;
                        }
                    } else {
                        if(isset(self::TOKEN_CLOSE_MAP[$token[0]])){
                            $p_stack[]= $token[0];
                        }
                    }
                }
                if($parentheses){
                    array_shift($tokens);
                    $trim = true;
                } else {
                    $tokens []= $end;
                }
            } else if(isset($end) && !$trim) {
                $tokens []= $end;
            }
            unset($end);
        }while($trim);
        return $tokens;
    }

    public function isAllSimple(){
        foreach ($this->arguments as $arg){
            if(!$arg->isSimple()){
                return false;
            }
        }
        return true;
    }

    public function toArray(){
        return array_map(function(Argument $arg){
            return $arg->toString();
        }, $this->arguments);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->arguments[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return Argument
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->arguments[$offset];
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new Exception("This collection is immutable.");
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     * @throws Exception
     */
    public function offsetUnset($offset)
    {
        throw new Exception("This collection is immutable.");
    }

    /**
     * Count elements of an object
     * @link https://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->arguments);
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator()
    {
        for ($i = 0; $i < $this->count(); $i ++){
            yield $this->offsetGet($i);
        }
    }
}