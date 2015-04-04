<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

/**
 * Unary NOT Operator -- Javascript '!' Operator
 */
class c_bit_not extends BaseUnaryConstruct
{
    /**
     * @param BaseConstruct[] $expression
     */
    function __construct($expression)
    {
        parent::__construct([$expression], true);
    }
}

