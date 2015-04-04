<?php

namespace js4php5\compiler\constructs;

class c_literal_null extends BaseConstruct
{
    function emit($unusedParameter = false)
    {
        return 'Runtime::$null';
    }
}

