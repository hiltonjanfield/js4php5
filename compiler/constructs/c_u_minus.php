<?php

namespace js4php5\compiler\constructs;

use js4php5\VarDumper;

class c_u_minus extends BaseUnaryConstruct
{
    /**
     * @param BaseConstruct[] $expression
     */
    function __construct($expression)
    {
        parent::__construct([$expression], true);
    }
}

