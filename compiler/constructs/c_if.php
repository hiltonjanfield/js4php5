<?php

namespace js4php5\compiler\constructs;

class c_if extends BaseConstruct
{
    function __construct($cond, $ifblock, $elseblock = null)
    {
        $this->cond = $cond;
        $this->ifblock = $ifblock;
        $this->elseblock = $elseblock;
    }

    function emit($unusedParameter = false)
    {
        $o = "if (Runtime::js_bool(" . $this->cond->emit(true) . ")) " . $this->ifblock->emit(true);
        if ($this->elseblock) {
            $o = rtrim($o) . " else " . $this->elseblock->emit(true) . "\n";
        }
        return $o;
    }
}

