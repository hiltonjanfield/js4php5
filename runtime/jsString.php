<?php

namespace js4php5\runtime;

use js4php5\JS;

class jsString extends jsObject
{
    function __construct($value = null)
    {
        parent::__construct("String", Runtime::$proto_string);
        if ($value == null or $value == Runtime::$undefined) {
            $this->value = Runtime::js_str("");
        } else {
            $this->value = $value->toStr();
        }
        $len = strlen($this->value->value);
        if (Runtime::$proto_string != null) {
            $this->put("length", Runtime::js_int($len), array("dontenum", "dontdelete", "readonly"));
        }
    }

    static public function object($value)
    {
        if (jsFunction::isConstructor()) {
            return new jsString($value);
        } else {
            if ($value == Runtime::$undefined) {
                return Runtime::js_str("");
            }
            return $value->toStr();
        }
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////

    static public function fromCharCode($c)
    {
        $args = func_get_args();
        $s = '';
        foreach ($args as $arg) {
            $v = $arg->toUInt16()->value;
            $s .= chr($v); // XXX fails if $v>255
        }
        return Runtime::js_str($s);
    }

    static public function toString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsString)) {
            throw new jsException(new jsTypeError());
        }
        return $obj->value;
    }

    static public function charAt($pos)
    {
        $str = Runtime::this()->toStr()->value;
        $pos = $pos->toInteger()->value;
        if ($pos < 0 || strlen($str) <= $pos) {
            return Runtime::js_str("");
        }
        return Runtime::js_str($str[$pos]);
    }

    static public function charCodeAt($pos)
    {
        $str = Runtime::this()->toStr()->value;
        $pos = $pos->toInteger()->value;
        if ($pos < 0 || strlen($str) <= $pos) {
            return Runtime::$nan;
        }
        return Runtime::js_int(ord($str[$pos]));
    }

    static public function concat($str)
    {
        $str = Runtime::this()->toStr()->value;
        $args = func_get_args();
        foreach ($args as $arg) {
            $str .= $arg->toStr()->value;
        }
        return Runtime::js_str($str);
    }

    static public function indexOf($str, $pos)
    {
        $obj = Runtime::this()->toStr()->value;
        $str = $str->toStr()->value;
        $pos = $pos->toInteger()->value;
        $v = strpos($obj, $str, $pos);
        if ($v === false) {
            return Runtime::js_int(-1);
        }
        return Runtime::js_int($v);
    }

    static public function lastIndexOf($str, $pos)
    {
        $obj = Runtime::this()->toStr()->value;
        $str = $str->toStr()->value;
        $pos = $pos->toNumber()->value;
        if (is_nan($pos)) {
            $pos = strlen($obj);
        }
        $v = strrpos($obj, $str, $pos);
        if ($v === false) {
            return Runtime::js_int(-1);
        }
        return Runtime::js_int($v);
    }

    static public function localeCompare($that)
    {
        $obj = Runtime::this();
        return Runtime::js_int(strcoll($obj->toStr()->value, $that->toStr()->value));
    }

    static public function match($regexp)
    {
        $obj = Runtime::this()->toStr();
        if (!($regexp instanceof jsRegexp)) {
            $regexp = new jsRegexp($regexp->toStr()->value);
        }
        if ($regexp->get("global") == Runtime::false) {
            return Runtime::$proto_regexp->get("exec")->_call($regexp, $obj);
        } else {
            $regexp->put("lastIndex", Runtime::$zero);
            // XXX finish once the RegExp stuff is written # 15.5.4.10
            throw new jsException(new jsError("string::match not implemented"));
        }
    }

    static public function replace($search, $replace)
    {
        $obj = Runtime::this()->toStr();
        // XXX finish once the Regexp stuff is written
        throw new jsException(new jsError("string::replace not implemented"));
    }

    static public function search($regexp)
    {
        $obj = Runtime::this()->toStr();
        if (JS::getClassNameWithoutNamespace($regexp) != "jsRegexp") {
            $regexp = new jsRegexp($regexp->toStr()->value);
        }
        // XXX finish once RegExp is there
        throw new jsException(new jsError("string::search not implemented"));
    }

    static public function slice($start, $end)
    {
        $obj = Runtime::this()->toStr()->value;
        $len = strlen($obj);
        $start = $start->toInteger()->value;
        $end = ($end == Runtime::$undefined) ? $len : $end->toInteger()->value;
        $start = ($start < 0) ? max($len + $start, 0) : min($start, $len);
        $end = ($end < 0) ? max($len + $end, 0) : min($end, $len);
        $len = max($end - $start, 0);
        $str = substr($obj, $start, $len);
        return Runtime::js_str($str);
    }

    static public function split($sep, $limit)
    {
        $obj = Runtime::this()->toStr()->value;
        $limit = ($limit == Runtime::$undefined) ? 0xffffffff : $limit->toUInt32()->value;
        if (!($regexp instanceof jsRegexp)) {
            // XXX finish me once RegExp is there
            throw new jsException(new jsError("string::split(//) not implemented"));
        }
        $sep = $sep->toStr()->value;
        $array = explode($sep, $obj);
        return new jsArray(count($array), $array);
    }

    static public function substr($start, $length)
    {
        $obj = Runtime::this()->toStr()->value;
        $len = strlen($obj);
        $start = $start->toInteger()->value;
        $length = ($length == Runtime::$undefined) ? 1e80 : $length->toInteger()->value;
        $start = ($start >= 0) ? $start : max($len + $start, 0);
        $length = min(max($length, 0), $len - $start);
        if ($length <= 0) {
            return Runtime::js_str("");
        }
        return Runtime::js_str(substr($obj, $start, $length));
    }

    static public function substring($start, $end)
    {
        $obj = Runtime::this()->toStr()->value;
        $len = strlen($obj);
        $start = $start->toInteger()->value;
        $end = ($end == Runtime::$undefined) ? $len : $end->toInteger()->value;
        $start = min(max($start, 0), $len);
        $end = min(max($end, 0), $len);
        $len = max($start, $end) - min($start, $end);
        return Runtime::js_str(substr($obj, $start, $len));
    }

    static public function toLocaleLowerCase()
    {
        // the i18n force is not strong with this one.
        return jsString::toLowerCase();
    }

    static public function toLowerCase()
    {
        return Runtime::js_str(strtolower(Runtime::this()->toStr()->value));
    }

    static public function ToLocaleUpperCase()
    {
        return jsString::toUpperCase();
    }

    static public function toUpperCase()
    {
        return Runtime::js_str(strtoupper(Runtime::this()->toStr()->value));
    }

    function defaultValue($iggy = null)
    {
        return $this->value;
    }
}

?>