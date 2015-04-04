<?php

namespace js4php5\compiler\constructs;

class c_statement extends BaseConstruct
{

    /** @var c_assign|c_call */
    public $child;

    /**
     * @param c_assign|c_call $child
     */
    function __construct($child)
    {
        $this->child = $child;
    }

    /**
     * @param bool $unusedParameter
     *
     * @return string PHP Code Chunk
     */
    function emit($unusedParameter = false)
    {
        return $this->child->emit(true) . ";\n";
    }
}

