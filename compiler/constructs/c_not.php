<?php

namespace js4php5\compiler\constructs;

use js4php5\VarDumper;

class c_not extends BaseUnaryConstruct
{
    /**
     * @param BaseConstruct[] $expression
     */
    function __construct($expression)
    {
        parent::__construct([$expression], true);
    }
}

