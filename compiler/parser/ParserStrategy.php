<?php

namespace js4php5\compiler\parser;

use hiltonjanfield\js4php5\compiler\lexer\Lexer;
use hiltonjanfield\js4php5\compiler\lexer\Token;

abstract class ParserStrategy
{
    abstract public function stuck(Token $token, Lexer $lex, $stack);

    abstract public function assertDone(Token $token, Lexer $lex);
}

