<?php

namespace js4php5\runtime;

class jsEvalError extends jsError
{
    function __construct($msg = '')
    {
        parent::__construct("EvalError", Runtime::$proto_evalerror, $msg);
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