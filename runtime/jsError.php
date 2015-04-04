<?php

namespace js4php5\runtime;


class jsError extends jsObject
{
    function __construct($class = "Error", $proto = null, $msg = '')
    {
        parent::__construct($class, ($proto == null) ? Runtime::$proto_error : $proto);
        $this->put("name", Runtime::js_str($class));
        $this->put("message", Runtime::js_str($msg));
    }

////////////////////////
// scriptable methods //
////////////////////////
    static function object($message)
    {
        return new jsError("Error", null, $message->toStr()->value);
    }

    static function toString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsError)) {
            throw new jsException(new js_typeeror());
        }
        return Runtime::js_str($obj::className() . ": " . $obj->get("message")->toStr()->value);
    }
}

?>