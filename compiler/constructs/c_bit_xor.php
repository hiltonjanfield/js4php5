<?php

namespace js4php5\compiler\constructs;

/**
 * Bitwise XOR Operator -- Javascript '^' Operator
 */
class c_bit_xor extends BaseBinaryConstruct
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

