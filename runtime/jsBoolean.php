<?php

namespace js4php5\runtime;

class jsBoolean extends jsObject
{
    function __construct($value = null)
    {
        parent::__construct("Boolean", Runtime::$proto_boolean);
        if ($value == null) {
            $value = Runtime::$undefined;
        }
        $this->value = $value->toBoolean();
    }

    static public function object($value)
    {
        if (jsFunction::isConstructor()) {
            return new jsBoolean($value);
        } else {
            return $value->toBoolean();
        }
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////

    static public function toString()
    {
        $obj = Runtime::this();
        if (JS::getClassNameWithoutNamespace($obj) != "jsBoolean") {
            throw new jsException(new jsTypeError());
        }
        return $obj->value->value == Runtime::$true ? Runtime::js_str("true") : Runtime::js_str("false");
    }

    static public function valueOf()
    {
        $obj = Runtime::this();
        if (JS::getClassNameWithoutNamespace($obj) != "jsBoolean") {
            throw new jsException(new jsTypeError());
        }
        return $obj->value;
    }

    function defaultValue($iggy = null)
    {
        return $this->value;
    }

}

?>