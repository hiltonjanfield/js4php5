<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

/**
 * Construct to emulate JavaScript post-plus-plus operator (var++)
 */
class c_post_pp extends BaseUnaryConstruct
{
    /**
     * @param c_identifier $identifier
     */
    function __construct($identifier)
    {
        parent::__construct([$identifier]);
    }
}

