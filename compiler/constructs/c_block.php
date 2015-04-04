<?php

namespace js4php5\compiler\constructs;

class c_block extends BaseConstruct
{
    /** @var c_statement[] */
    public $statements;

    /**
     * @param c_statement[] $statements
     */
    function __construct($statements)
    {
        $this->statements = $statements;
    }

    function emit($unusedParameter = false)
    {
        $o = "{\n";
        /** @var c_statement $statement */
        foreach ($this->statements as $statement) {
            $o .= "  " . trim(str_replace("\n", "\n  ", $statement->emit(true))) . "\n";
        }
        $o .= "}\n";
        return $o;
    }
}

