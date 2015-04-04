<?php

namespace js4php5\compiler\constructs;

class c_void extends BaseUnaryConstruct
{
    /**
     * @param BaseConstruct[] $expression
     */
    function __construct($expression)
    {
        parent::__construct([$expression], true);
    }
}

