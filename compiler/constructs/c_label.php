<?php

namespace js4php5\compiler\constructs;

use js4php5\VarDumper;

class c_label extends BaseConstruct
{

    /**
     * @var string
     */
    public $label;

    /**
     * @var c_statement|c_block
     */
    public $block;

    /**
     * @param string $label
     * @param c_statement|c_block $block
     */
    function __construct($label, $block)
    {
        $this->label = $label;
        $this->block = $block;

        $p = explode(':', $this->label);
        $this->label = $p[0];
    }

    function emit($unusedParameter = false)
    {
        // associate this label with current $nest;
        c_source::$labels[$this->label] = c_source::$nest;

        //return "/* ".$this->label." */ ".$this->block->emit();
        return $this->block->emit(true);
    }
}

