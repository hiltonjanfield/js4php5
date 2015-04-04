<?php

namespace js4php5\compiler\constructs;

class c_break extends BaseConstruct
{
    /** @var string */
    public $label;

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
            return "ERROR: break outside of a loop\n*************************\n\n";
        }
        if ($this->label !== ';') {
            $depth = c_source::$nest - c_source::$labels[$this->label];
            $o = "break $depth;\n";
        } else {
            $o = "break;\n";
        }
        return $o;
    }
}

