<?php

namespace js4php5\compiler\constructs;

class c_multiply extends BaseBinaryConstruct
{
    /**
     * @param BaseConstruct[] $leftStatement
     * @param BaseConstruct[] $rightStatement
     */
    function __construct($leftStatement, $rightStatement)
    {
        parent::__construct([$leftStatement, $rightStatement], true, true);
    }
}

