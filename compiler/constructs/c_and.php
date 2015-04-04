<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\compiler\Compiler;

/**
 * Logical AND Construct - Javascript '&&' operator
 */
class c_and extends BaseBinaryConstruct
{

    /**
     * @param BaseConstruct[] $leftStatement
     * @param BaseConstruct[] $rightStatement
     */
    function __construct($leftStatement, $rightStatement)
    {
        parent::__construct([$leftStatement, $rightStatement], true, true);
    }

    /**
     * @inheritdoc
     */
    function emit($unusedParameter = false)
    {
        $symbol = Compiler::generateSymbol("sc");
        return "(!Runtime::js_bool(\$$symbol=" . $this->arg1->emit(true) . ")?\$$symbol:" . $this->arg2->emit(true) . ")";
    }
}
