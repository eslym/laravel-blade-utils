<?php


namespace Eslym\BladeUtils\Tools;


use ArrayAccess;
use Countable;
use Exception;

class Arguments implements ArrayAccess, Countable
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

    private $p_stack = [];

    /**
     * @var Argument[]
     */
    private $arguments = [];

    public function __construct($expression)
    {
        $tokens = token_get_all('<?php '.$expression);
        $arguments = [[]];
        array_shift($tokens);
        while($token = array_shift($tokens)){
            if(is_string($token)){
                if($token == ',' && empty($this->p_stack)){
                    $last = end($arguments[0]);
                    if(is_array($last) && $last[0] == T_WHITESPACE){
                        array_pop($arguments[0]);
                    }
                    array_unshift($arguments, []);
                    continue;
                }
                $arguments[0][]=$token;
                $last = end($this->p_stack);
                if(isset(self::TOKEN_CLOSE_MAP[$last]) && self::TOKEN_CLOSE_MAP[$last] == $token){
                    array_pop($this->p_stack);
                } else if(isset(self::TOKEN_CLOSE_MAP[$token])){
                    $this->p_stack[]= $token;
                }
            } else {
                if(empty($arguments[0]) && $token[0] == T_WHITESPACE){
                    continue;
                }
                $arguments[0][]=$token;
                if(isset(self::TOKEN_CLOSE_MAP[$token[0]])){
                    $this->p_stack[]= $token[0];
                }
            }
        }
        $this->arguments = array_reverse(array_map(function($tokens){
            return new Argument($tokens);
        }, $arguments));
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
}