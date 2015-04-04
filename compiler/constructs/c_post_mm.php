<?php

namespace js4php5\compiler\constructs;

/**
 * Construct to emulate JavaScript post-minus-minus operator (var--)
 */
class c_post_mm extends BaseUnaryConstruct
{
    /**
     * @param c_identifier $identifier
     */
    function __construct($identifier)
    {
        parent::__construct([$identifier]);
    }
}

