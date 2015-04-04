<?php

namespace js4php5\compiler\constructs;

class c_literal_number extends BaseConstruct
{
    function __construct($v)
    {
        $this->v = $v;
    }

    function emit($unusedParameter = false)
    {
        return "Runtime::js_int(" . $this->v . ")";
    }
}

