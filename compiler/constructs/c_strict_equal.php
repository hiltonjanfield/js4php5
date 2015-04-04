<?php

namespace js4php5\compiler\constructs;

class c_strict_equal extends BaseBinaryConstruct
{
    function __construct()
    {
        parent::__construct(func_get_args(), true, true);
    }
}

