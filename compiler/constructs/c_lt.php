<?php

namespace js4php5\compiler\constructs;

class c_lt extends BaseBinaryConstruct
{
    /**
     * @param BaseConstruct[] $leftStatement
     * @param BaseConstruct[] $rightStatement
     */
    function __construct($leftStatement, $rightStatement)
    {
        parent::__construct([$leftStatement, $rightStatement], true, true);
    }

    function emit($unusedParameter = false)
    {
        //TODO: Remove this? Other similar classes do not have this.
        //TODO: Test speed; consider ADDING to other BaseBinaryConstruct classes instead.
        // weak attempt at speeding things. probably not worth it.
        return "Runtime::cmp(" . $this->arg1->emit(true) . "," . $this->arg2->emit(true) . ", 1)";
    }
}

