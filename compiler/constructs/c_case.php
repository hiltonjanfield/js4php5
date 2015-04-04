<?php

namespace js4php5\compiler\constructs;

use js4php5\VarDumper;

class c_case extends BaseConstruct
{

    /** @var BaseConstruct */
    public $expr;

    /** @var c_statement[] */
    public $code;

    /**
     * @param BaseConstruct $expr
     * @param c_statement[] $code
     */
    function __construct($expr, $code)
    {
        $this->expr = $expr;
        $this->code = $code;
    }

    function emit($unusedParameter = false)
    {
        if (is_int($this->expr) && $this->expr == 0) {
            $o = "  default:\n";
        } else {
            $o = "  case (Runtime::js_bool(Runtime::expr_strict_equal(\$" . $this->e . "," . $this->expr->emit(true) . "))):\n";
        }
        foreach ($this->code as $code) {
            $o .= "    " . trim(str_replace("\n", "\n    ", $code->emit(true))) . "\n";
        }
        return $o;
    }
}

