<?php

namespace js4php5\compiler\constructs;

class c_new extends BaseConstruct
{

    /**
     * @var BaseConstruct
     */
    public $expression;

    function __construct($expression)
    {
        $this->expression = $expression;

        #-- if direct child is a c_call object, vampirize it.
//        if (JS::getClassNameWithoutNamespace($this->expression) == "c_call") {
        if ($this->expression instanceof c_call) {
            $this->args = $this->expression->args;
            $this->expression = $this->expression->expr;
        } else {
            $this->args = array();
        }
    }

    function emit($unusedParameter = false)
    {
        $args = array();
        foreach ($this->args as $arg) {
            $args[] = $arg->emit(true);
        }
        return "Runtime::_new(" . $this->expression->emit(true) . ", array(" . implode(",", $args) . "))";
    }
}

