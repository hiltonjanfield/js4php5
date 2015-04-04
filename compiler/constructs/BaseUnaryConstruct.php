<?php

namespace js4php5\compiler\constructs;

use js4php5\JS;
use js4php5\VarDumper;

abstract class BaseUnaryConstruct extends BaseConstruct
{
    /** @var BaseConstruct */
    public $arg;

    /** @var bool */
    public $getValue;

    public $runtime_op;

    /**
     * @param BaseConstruct[] $args
     * @param bool                $getValue
     */
    function __construct($args, $getValue = false)
    {
        $this->arg = $args[0];
        $this->getValue = $getValue;

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
        return "Runtime::expr_" . $this->runtime_op . "(" . $this->arg->emit($this->getValue) . ")";
    }
}

