<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

class c_call extends BaseConstruct
{
    /** @var c_identifier */
    public $expr;

    /** @var c_identifier[] */
    public $args;

    /**
     * @param c_identifier $expr
     * @param c_identifier[] $args
     */
    function __construct($expr, $args)
    {
        $this->expr = $expr;
        $this->args = $args;
    }

    function emit($unusedParameter = false)
    {
        $args = array();
        /** @var c_identifier $arg */
        foreach ($this->args as $arg) {
            $args[] = $arg->emit(true);
        }
        return "Runtime::call(" . $this->expr->emit() . ", array(" . implode(",", $args) . "))";
    }
}


