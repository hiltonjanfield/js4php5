<?php

namespace js4php5\runtime;

class jsUriError extends jsError
{
    function __construct($msg = '')
    {
        parent::__construct("URIError", Runtime::$proto_urierror, $msg);
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