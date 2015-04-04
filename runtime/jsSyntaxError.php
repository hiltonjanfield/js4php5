<?php

namespace js4php5\runtime;

class jsSyntaxError extends jsError
{
    function __construct($msg = '')
    {
        parent::__construct("SyntaxError", Runtime::$proto_syntaxerror, $msg);
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////
    static function object($message)
    {
        return new self($message->toStr()->value);
    }
}

?>