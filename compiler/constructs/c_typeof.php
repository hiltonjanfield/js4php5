<?php

namespace js4php5\compiler\constructs;

class c_typeof extends BaseUnaryConstruct
{
    /**
     * @param BaseConstruct[] $identifier
     */
    function __construct($identifier)
    {
        parent::__construct([$identifier], true);
    }
}

