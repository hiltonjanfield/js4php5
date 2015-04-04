<?php

namespace js4php5\compiler\constructs;

use js4php5\JS;

class c_ternary extends BaseConstruct
{
    public $expression;
    public $trueStatement;
    public $falseStatement;
    public $runtime_op;

    /**
     * @param BaseConstruct $expression
     * @param BaseConstruct $trueStatement
     * @param BaseConstruct $falseStatement
     */
    function __construct($expression, $trueStatement, $falseStatement)
    {
        $this->expression = $expression;
        $this->trueStatement = $trueStatement;
        $this->falseStatement = $falseStatement;
        $this->runtime_op = substr($this->className(), 3);
    }

    function emit($unusedParameter = false)
    {
        #-- can't use a helper function to maintain the short-circuit thing.
        return
            '(Runtime::js_bool(' .
            $this->expression->emit(true) .
            ')?(' .
            $this->trueStatement->emit(true) .
            '):(' .
            $this->falseStatement->emit(true) .
            '))';
    }
}

