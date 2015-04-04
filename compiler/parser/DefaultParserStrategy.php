<?php

namespace js4php5\compiler\parser;

use hiltonjanfield\js4php5\compiler\lexer\Lexer;
use hiltonjanfield\js4php5\compiler\lexer\Token;

class DefaultParserStrategy extends ParserStrategy
{

    /**
     * @param Token $token
     * @param Lexer $lex
     *
     * @throws ParseException
     */
    public function assertDone(Token $token, Lexer $lex)
    {
        if ($token->getType()) {
            $this->stuck($token, $lex, array());
        }
    }

    /**
     * @param Token $token
     * @param Lexer $lex
     * @param       $stack
     *
     * @throws ParseException
     */
    public function stuck(Token $token, Lexer $lex, $stack)
    {
        throw new ParseException(
            "Parser stuck; source and grammar do not agree. Can't tell what to do with token.",
            $token,
            $token->getStart());

//        Helpers::send_parse_error_css_styles();
//
?>
<!--        <hr/>The LR parser is stuck. Source and grammar do not agree.<br/>-->
<!--        Looking at token:-->
<!--        --><?php
//        Helpers::span('term', $token->text, $token->type);
//        echo ' [ ' . $token->type . ' ]';
//        echo "<br/>\n";
//        $lex->report_error();
//        echo "<hr/>\n";
//        echo "Backtrace Follows:<br/>\n";
//        # pr($stack);
//        while (count($stack)) {
//            $tos = array_pop($stack);
//            echo $tos->trace() . "<br/>\n";
//        }
//        throw new parse_error("Can't tell what to do with " . $token->type . ".");
    }
}
