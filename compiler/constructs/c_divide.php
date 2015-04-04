<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

class c_divide extends BaseBinaryConstruct
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

