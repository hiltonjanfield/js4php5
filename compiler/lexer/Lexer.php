<?php
/**
 * js4php5 Lexer - Reads in JavaScript source and outputs a tokenized result.
 *
 * Based on parser included with j4p5 which is reportedly from MetaPHP, a project abandoned in 2004.
 */

namespace js4php5\compiler\lexer;

use js4php5\compiler\jsly;

class Lexer
{

    /** @var array[] */
    private $pattern;

    private $state;

    private $initContext;

    private $context;

    /** @var Stream */
    private $stream;

    /** @var string */
    private $megaRegexp;

    public function __construct($init_context, $lexerPatterns = null)
    {
        $this->pattern = $lexerPatterns ? $lexerPatterns : array('INITIAL' => array());
        $this->state = 'INITIAL';
        $this->initContext = $init_context;
        $this->context = $init_context;
    }

//    function report_instant_description()
//    {
//        echo "Scanner State: $this->state<br/>\n";
//    }

    /**
     * @param string $name
     * @param array  $cluster
     */
//    function addState($name, array $cluster)
//    {
//        $this->pattern[$name] = $cluster;
//    }

    /**
     * @param string $string String to be tokenized.
     *
     * @return void
     */
    public function start($string)
    {
        $this->context = $this->initContext;
        $this->stream = new Stream($string);
        $this->megaRegexp = array();

        foreach ($this->pattern as $key => $details) {
            $s = '';
            foreach ($details as $pattern) {
                if ($s) {
                    $s .= '|';
                }
                $s .= $pattern[0];
            }
            $this->megaRegexp[$key] = '(' . $s . ')';
        }
    }

    /**
     * @return Token
     * @throws LexerException
     */
    public function next()
    {
        if (!is_array($this->pattern[$this->state])) {
            throw new LexerException("No lexer state called '{$this->state}''.");
        }

        $start = $this->stream->pos();

        if ($match = $this->stream->test($this->megaRegexp[$this->state])) {
            $text = $match[0];
            //TODO: WTH are these two lines for? There should be a simpler way to get the index.
            $tmp = array_flip($match);
            $index = $tmp[$text] - 1;
            $pattern = $this->pattern[$this->state][$index];
            //TODO: Do we need these local variables?
            $type = $pattern[1]; //->type;
            $action = $pattern[3]; //->action;
            if ($action) {
                jsly::$action($type, $text, $match, $this->state, $this->context);
            }
            if ($pattern[2]) {
                return $this->next();
            }
            $stop = $this->stream->pos();

            return new Token($type, $text, $start, $stop);
        }
        return $this->stream->defaultRule();
    }

}
