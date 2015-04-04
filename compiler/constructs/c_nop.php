<?php

namespace js4php5\compiler\constructs;

class c_nop extends BaseConstruct
{
    function emit($unusedParameter = false)
    {
        return '';
    }
}

