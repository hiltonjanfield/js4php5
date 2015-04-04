<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

class c_do extends BaseConstruct
{
    public $expr;
    public $statement;

    /**
     * @param BaseConstruct $expr
     * @param c_block $statement
     */
    function __construct($expr, $statement)
    {
        $this->expr = $expr;
        $this->statement = $statement;
    }

    function emit($unusedParameter = false)
    {
        c_source::$nest++;
        $o = "do " . rtrim($this->statement->emit(true)) . " while (Runtime::js_bool(" . $this->expr->emit(true) . "));\n";
        c_source::$nest--;
        return $o;
    }
}

