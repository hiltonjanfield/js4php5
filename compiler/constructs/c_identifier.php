<?php

namespace js4php5\compiler\constructs;

/**
 * JavaScript Identifier
 */
class c_identifier extends BaseConstruct
{
    /** @var string */
    public $id;

    /**
     * @param string $id
     */
    function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param bool $getValue
     *
     * @return string
     */
    function emit($getValue = false)
    {
        $v = $getValue ? "v" : "";
        return "Runtime::id$v('{$this->id}')";
    }
}

