<?php

namespace js4php5\compiler\constructs;

class c_this extends BaseConstruct
{
    function emit($unusedParameter = false)
    {
        return "Runtime::this()"; // should this be a Runtime::$this instead?
    }
}

