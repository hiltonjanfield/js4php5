<?php

namespace js4php5\compiler\constructs;

use js4php5\JS;

class c_literal_array extends BaseConstruct
{
    function __construct($arr)
    {
        $this->arr = $arr;
    }

    function emit($unusedParameter = false)
    {
        $a = array();
        for ($i = 0; $i < count($this->arr); $i++) {
            if ($this->arr[$i] != null) {
                $a[$i] = $this->arr[$i]->emit(true);
            }
        }
        if (count($this->arr) == 1 and ($this->arr[0] instanceof c_literal_null)) {
            $a = array();
        }

        return "Runtime::literal_array(" . implode(",", $a) . ")";
    }
}

