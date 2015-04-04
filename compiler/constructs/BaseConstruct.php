<?php

namespace js4php5\compiler\constructs;

/**
 * Abstract class for all Javascript construct classes
 */
abstract class BaseConstruct
{

    /**
     * @param bool $getValue True to return value, false for reference.
     *
     * @return string PHP Code Chunk
     */
    abstract function emit($getValue = false);

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        $x = explode('\\', get_called_class());
        return end($x);
    }

}
