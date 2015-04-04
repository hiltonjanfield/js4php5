<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\JS;

class c_print extends BaseConstruct
{
    /** @var BaseConstruct[] */
    public $args;

    function __construct()
    {
        $this->args = func_get_args();
    }

    function emit($unusedParameter = false)
    {
        $o = 'Runtime::write( ';
        $first = true;
        foreach ($this->args as $arg) {
            if ($first) {
                $first = false;
            } else {
                $o .= ",";
            }
            $o .= "(" . ($arg->className() ? $arg->emit(true) : $arg).")";
        }
        $o .= ");\n";
        return $o;
    }
}

