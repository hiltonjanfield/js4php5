<?php

namespace js4php5\compiler\lexer;

class Point
{

    /**
     * @var int
     */
    private $line;

    /**
     * @var int
     */
    private $col;

    /**
     * @param int $line
     * @param int $col
     */
    function __construct($line, $col)
    {
        $this->line = (int)$line;
        $this->col = (int)$col;
    }

    /**
     * @return int
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
}
