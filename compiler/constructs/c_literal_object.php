<?php

namespace js4php5\compiler\constructs;

class c_literal_object extends BaseConstruct
{
    function __construct($o = array())
    {
        $this->obj = $o;
    }

    function emit($unusedParameter = false)
    {
        $a = array();
        for ($i = 0; $i < count($this->obj); $i++) {
            $a[] = $this->obj[$i]->emit();
        }
        return "Runtime::literal_object(" . implode(",", $a) . ")";
    }
}

