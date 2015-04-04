<?php

namespace js4php5\compiler\constructs;

use js4php5\compiler\Compiler;

class c_switch extends BaseConstruct
{
    function __construct($expr, $block)
    {
        list($this->expr, $this->block) = func_get_args();
    }

    function emit($unusedParameter = false)
    {
        $e = Compiler::generateSymbol("jsrt_sw");
        c_source::$nest++;
        $o = "\$$e = " . $this->expr->emit(true) . ";\n";
        $o .= "switch (true) {\n";
        foreach ($this->block as $case) {
            $case->e = $e;
            $o .= $case->emit(true);
        }
        $o .= "\n}\n";
        c_source::$nest--;
        return $o;
    }
}

