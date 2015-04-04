<?php

namespace js4php5\compiler\lexer;

class Token
{

    private $type;

    private $text;

    private $start;

    private $stop;

    function __construct($type, $text, Point $start, Point $stop)
    {
        $this->type = $type;
        $this->text = $text;
        $this->start = $start;
        $this->stop = $stop;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return mixed
     */
    public function getStop()
    {
        return $this->stop;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    static public function getNullToken() {
        $point = new Point(-1, -1);
        return new Token('', '', $point, $point);
    }
}

