<?php

namespace js4php5\runtime;

class jsTypeError extends jsError
{
    function __construct($msg = '')
    {
        parent::__construct("TypeError", Runtime::$proto_typeerror, $msg);
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