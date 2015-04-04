<?php

namespace js4php5\compiler\constructs;

class c_throw extends BaseConstruct
{
    /* JS exceptions are sufficiently different from php5 exceptions to make them un-leverage-able. */
    function __construct($expr)
    {
        $this->expr = $expr;
    }

    function emit($unusedParameter = false)
    {
        //return "return new js_completion(".$this->expr->emit().");\n";
        return "throw new jsException(" . $this->expr->emit(true) . ");\n";
    }
}

