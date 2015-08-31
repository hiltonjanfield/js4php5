<?php

namespace js4php5\compiler\parser;

use js4php5\compiler\jsly;
use js4php5\compiler\lexer\Lexer;
use js4php5\jsc\js_program;

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
     * @param string              $symbol
     * @param Lexer               $lex
     * @param null|ParserStrategy $strategy
     *
     * @return js_program
     *
     * @throws parse_error
     */
    public function parse($symbol, Lexer $lex, ParserStrategy $strategy = null)
    {
        return parent::parse($symbol, $lex, $this->strategy);
    }
}


