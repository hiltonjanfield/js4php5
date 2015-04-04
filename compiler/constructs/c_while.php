<?php

namespace js4php5\compiler\constructs;

class c_while extends BaseConstruct
{
    function __construct($expr, $statement)
    {
        $this->expr = $expr;
        $this->statement = $statement;
    }

    function emit($unusedParameter = false)
    {
        c_source::$nest++;
        $o = "while (Runtime::js_bool(" . $this->expr->emit(true) . ")) " . $this->statement->emit(true) . "\n";
        c_source::$nest--;
        return $o;
    }
}

