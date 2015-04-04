<?php

namespace js4php5\compiler\constructs;

use js4php5\VarDumper;

class c_compound_assign extends BaseConstruct
{
    public $a;
    public $b;
    public $op;

    /**
     * @param c_identifier $a
     * @param BaseConstruct $b
     * @param string $op
     */
    function __construct($a, $b, $op)
    {
        $this->a = $a;
        $this->b = $b;
        $this->op = $op;
    }

    /**
     * @param bool $unusedParameter
     *
     * @return string PHP code chunk
     */
    function emit($unusedParameter = false)
    {
        switch ($this->op) {
            case '*=':
                $s = "expr_multiply";
                break;
            case '/=':
                $s = "expr_divide";
                break;
            case '%=':
                $s = "expr_modulo";
                break;
            case '+=':
                $s = "expr_plus";
                break;
            case '-=':
                $s = "expr_minus";
                break;
            case '<<=':
                $s = "expr_lsh";
                break;
            case '>>=':
                $s = "expr_rsh";
                break;
            case '>>>=':
                $s = "expr_ursh";
                break;
            case '&=':
                $s = "expr_bit_and";
                break;
            case '^=':
                $s = "expr_bit_xor";
                break;
            case '|=':
                $s = "expr_bit_or";
                break;
        }
        return "Runtime::expr_assign(" . $this->a->emit() . "," . $this->b->emit(true) . ",'" . $s . "')";
    }
}

