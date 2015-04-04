<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

class c_for extends BaseConstruct
{
    public $init;
    public $condition;
    public $increment;
    public $statement;

    /**
     * @param c_var|c_assign $init
     * @param BaseBinaryConstruct|c_call $condition
     * @param BaseConstruct $increment
     * @param c_block|c_statement $statement
     */
    function __construct($init, $condition, $increment, $statement)
    {
        $this->init = $init;
        $this->condition = $condition;
        $this->increment = $increment;
        $this->statement = $statement;
    }

    function emit($unusedParameter = false)
    {
        $o = $this->init ? $this->init->emit(true) : '';
        c_source::$nest++;
        $o .= "for (;" . ($this->condition ? "Runtime::js_bool(" . $this->condition->emit(true) . ")" : '');
        $o .= ";" . ($this->increment ? $this->increment->emit(true) : '') . ") {\n";
        $o .= $this->statement->emit(true);
        $o .= "\n}\n";
        c_source::$nest--;
        return $o;
    }
}

