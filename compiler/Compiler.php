<?php
/**
 * Main API file for compiling JavaScript into native PHP.
 *
 * This class is used by the root JS class and is not intended to be used directly,
 * however the compile() method can be called if a person wishes to pre-compile scripts
 * or use them for some other nefarious purpose.
 */

namespace js4php5\compiler;


use hiltonjanfield\js4php5\compiler\lexer\Lexer;
use hiltonjanfield\js4php5\compiler\parser\EasyParser;

class Compiler {

    static public function generateSymbol($prefix = '')
    {
        static $uniq = 0;
        return $prefix . ++$uniq;
    }

    static public function compile($javascript) {
        static $lex = null;
        static $parser = null;

        if ($lex === null) {
            $lex = new Lexer(0, jsly::$lexp);
            $parser = new EasyParser(jsly::$dpa);
        }

        $lex->start($javascript);
        $program = $parser->parse('c_program', $lex);

        // Convert into usable php code
        try {
            $php = $program->emit();
        } catch (\Exception $e) {
            #-- Compilation error. should be pretty rare. usually the parser will barf long before this.
            echo "Compilation Error: " . $e->getMessage() . "<hr>";
        }
        return $php;
    }

} 