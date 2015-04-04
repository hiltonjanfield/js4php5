<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

/**
 * js4php5
 * Abstract base class for binary and bitwise operators (&&, ||, &, |, ^, etc.)
 */
abstract class BaseBinaryConstruct extends BaseConstruct
{

    /** @var BaseConstruct */
    public $arg1;

    /** @var BaseConstruct */
    public $arg2;

    /** @var bool */
    public $getValue1;

    /** @var bool */
    public $getValue2;

    /** @var string */
    public $runtime_op;

    /**
     * @param BaseConstruct[] $args
     * @param bool            $getValue1
     * @param bool            $getValue2
     */
    function __construct($args, $getValue1 = false, $getValue2 = false)
    {
        $this->arg1 = $args[0];
        $this->arg2 = $args[1];
        $this->getValue1 = $getValue1;
        $this->getValue2 = $getValue2;

        // Requires compiler construct files be prefixed c_
        preg_match('/c_([A-Za-z_]+)$/', $this->className(), $match);
        $this->runtime_op = $match[1];
    }

    /**
     * @param bool $unusedParameter Ignored.
     *
     * @return string PHP code chunk
     */
    function emit($unusedParameter = false)
    {
//        return "Runtime::expr_" . $this->runtime_op . "(" . $this->arg1->emit($this->getValue1) . "," . $this->arg2->emit($this->getValue2) . ")";

        // Hack to deal with $arg2 being null (happens when variables are declared without a value: 'var foobar;')
        $php =
            'Runtime::expr_' .
            $this->runtime_op .
            '(' .
            $this->arg1->emit($this->getValue1) .
            ',';
        if ($this->arg2 === null) {
            $php .= 'null';
        } else {
            $php .= $this->arg2->emit($this->getValue2);
        }
        $php .= ')';

        return $php;
    }
}
