<?php

namespace js4php5\compiler\parser;

class ParseStackFrame
{

    private $symbol;
    private $semantic;

    public $state;

    /**
     * @param string $symbol
     * @param array $state
     */
    function __construct($symbol, $state)
    {
        $this->symbol = $symbol;
        $this->state = $state;
        $this->semantic = array();
    }

    function shift($semantic)
    {
        $this->semantic[] = $semantic;
    }

    function fold($semantic)
    {
        $this->semantic = array($semantic);
    }

    function semantic()
    {
        return $this->semantic;
    }

    function trace()
    {
        return "$this->symbol : $this->state";
    }
}

