<?php

namespace js4php5\compiler\constructs;

class c_continue extends BaseConstruct
{

    /**
     * @param string $label
     */
    function __construct($label)
    {
        $this->label = $label;
    }

    function emit($unusedParameter = false)
    {
        if (c_source::$nest == 0) {
            return "ERROR: continue outside of a loop\n*************************\n\n";
        }
        if ($this->label !== ';') {
            $depth = c_source::$nest - c_source::$labels[$this->label];
            $o = "continue $depth;\n";
        } else {
            $o = "continue;\n";
        }
        return $o;
    }
}

