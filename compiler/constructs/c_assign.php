<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

/**
 * Assignment Operator -- Javascript '='
 */
class c_assign extends BaseBinaryConstruct
{

    /**
     * @param BaseConstruct[] $leftStatement
     * @param BaseConstruct[] $rightStatement
     */
    function __construct($leftStatement, $rightStatement)
    {
        parent::__construct([$leftStatement, $rightStatement], false, true);
    }
}

