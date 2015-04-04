<?php

namespace js4php5\compiler\constructs;

class c_literal_boolean extends BaseConstruct
{
    function __construct($v)
    {
        $this->v = $v;
    }

    function emit($unusedParameter = false)
    {
        return $this->v ? 'Runtime::$true' : 'Runtime::$false';
    }
}

