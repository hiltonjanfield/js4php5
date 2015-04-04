<?php

namespace js4php5\runtime;

class jsRangeError extends jsError
{
    function __construct($msg = '')
    {
        parent::__construct("RangeError", Runtime::$proto_rangeerror, $msg);
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