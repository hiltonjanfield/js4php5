<?php

namespace js4php5\compiler\constructs;

/**
 * Construct to emulate JavaScript pre-minus-minus operator (--var)
 */
class c_pre_mm extends BaseUnaryConstruct
{
    /**
     * @param c_identifier $identifier
     */
    function __construct($identifier)
    {
        parent::__construct([$identifier]);
    }
}

