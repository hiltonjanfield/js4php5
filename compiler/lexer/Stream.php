<?php

namespace js4php5\compiler\lexer;

class Stream
{

    /**
     * @var string
     */
    private $string;

    /**
     * @var int
     */
    private $col;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $string
     */
    function __construct($string)
    {
        $this->string = $string;
        $this->col = 0;
        $this->line = 1;
    }

    /**
     * @param string $str
     *
     * @return void
     */
    private function consume($str)
    {
        $len = strlen($str);
        $this->string = substr($this->string, $len);
        $this->col += $len;
    }

    /**
     * @param string $pattern Perl Regex pattern to test with.
     *
     * @return string[]|false
     */
    public function test($pattern)
    {
        if (preg_match($pattern . 'A', $this->string, $match)) {
            $this->consume($match[0]);
            return $match;
        }

        return false;
    }

    /**
     * @return Token
     */
    public function defaultRule()
    {
        if (!strlen($this->string)) {
            return Token::getNullToken();
        }

        $start = $this->pos();
        $ch = $this->string[0];
        $this->consume($ch);
        $stop = $this->pos();
        return new Token('c' . $ch, $ch, $start, $stop);
    }

    /**
     * @return Point
     */
    public function pos()
    {
        return new Point($this->line, $this->col);
    }
}

