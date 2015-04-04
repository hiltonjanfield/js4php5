<?php

namespace js4php5\compiler\parser;

use Exception;
use hiltonjanfield\js4php5\compiler\lexer\Point;
use hiltonjanfield\js4php5\compiler\lexer\Token;

class ParseException extends Exception
{
    private $token;
    private $pos;

    public function __construct($message = "", $token, Point $pos, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->token = $token;
        $this->pos = $pos;
    }

    /**
     * @return Point
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

}

