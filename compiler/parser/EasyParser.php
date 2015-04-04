<?php

namespace js4php5\compiler\parser;

use js4php5\compiler\jsly;
use js4php5\jsc\js_program;
use js4php5\VarDumper;

class EasyParser extends Parser
{
    private $call;
    private $strategy;

    function __construct($pda, $strategy = null)
    {
        parent::__construct($pda);
        $this->call = $this->action; //array();
        $this->strategy = ($strategy ? $strategy : new DefaultParserStrategy());
    }

    function reduce($action, $tokens)
    {
        $call = $this->call[$action];
        return jsly::$call($tokens);
    }


    /**
     * @param string                                       $symbol
     * @param \js4php5\compiler\lexer\Lexer $lex
     * @param null                                         $strategy
     *
     * @return js_program
     *
     * @throws parse_error
     */
    function parse($symbol, $lex, $strategy = null)
    {
        return parent::parse($symbol, $lex, $this->strategy);
    }
}


