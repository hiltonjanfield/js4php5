<?php

namespace js4php5\compiler\constructs;

/**
 * Construct to emulate JavaScript pre-plus-plus operator (++var)
 */
class c_pre_pp extends BaseUnaryConstruct
{
    /**
     * @param c_identifier $identifier
     */
    function __construct($identifier)
    {
        parent::__construct([$identifier]);
    }
}

