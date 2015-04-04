<?php

namespace js4php5\runtime;

class jsReferenceError extends jsError
{
    function __construct($msg = '')
    {
        parent::__construct("ReferenceError", Runtime::$proto_referenceerror, $msg);
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