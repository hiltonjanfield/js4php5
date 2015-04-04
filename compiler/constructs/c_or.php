<?php

namespace js4php5\compiler\constructs;

use js4php5\compiler\Compiler;

/**
 * Logical OR Construct - Javascript '||' operator
 */
class c_or extends BaseBinaryConstruct
{

    /**
     * @param BaseConstruct[] $leftStatement
     * @param BaseConstruct[] $rightStatement
     */
    function __construct($leftStatement, $rightStatement)
    {
        parent::__construct([$leftStatement, $rightStatement], false, false);
    }

    /**
     * @param bool $unusedParameter
     *
     * @return string PHP code chunk
     */
    function emit($unusedParameter = false)
    {
        $symbol = Compiler::generateSymbol("sc");
        return "(Runtime::js_bool(\$$symbol=" . $this->arg1->emit(true) . ")?\$$symbol:" . $this->arg2->emit(true) . ")";
    }
}
