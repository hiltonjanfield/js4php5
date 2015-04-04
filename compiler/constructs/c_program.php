<?php

namespace js4php5\compiler\constructs;

class c_program extends BaseConstruct
{

    /** @var c_source */
    static $source;

    /** @var c_source */
    public $src;

    /**
     * @param c_source $obj
     */
    function __construct(c_source $obj)
    {
        $this->src = $obj;
        self::$source = $obj;
    }

    /**
     * @param int $unused
     *
     * @return string The full, final code of the program.
     */
    function emit($unused = 0)
    {
        $source = $this->src->emit(true);

        return $source;
    }
}

