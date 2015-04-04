<?php

namespace js4php5\runtime;


class jsNumber extends jsObject
{
    function __construct($value = null)
    {
        parent::__construct("Number", Runtime::$proto_number);
        if ($value == null) {
            $value = Runtime::$zero;
        }
        $this->value = $value->toNumber();
    }

    static public function object($value)
    {
        if (jsFunction::isConstructor()) {
            return new jsNumber($value);
        } else {
            return $value->toNumber();
        }
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////

    static public function valueOf()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsNumber)) {
            throw new jsException(new jsTypeError());
        }
        return $obj->toNumber()->value;
    }

    static public function toExponential($digits)
    {
        $obj = Runtime::this();
        $f = $digits->toInteger()->value;
        if ($f < 0 || $f > 20) {
            throw js_exception(js_rangeerror());
        }
        $x = $obj->toNumber()->value;
        if (is_nan($x)) {
            return Runtime::js_str("NaN");
        }
        if (is_infinite($x)) {
            return jsNumber::toString();
        }
        return Runtime::js_str(sprintf("%." . (1 + $f) . "e", $x));
    }

    static public function toString()
    {
        list($radix) = func_get_args();
        $obj = Runtime::this();
        if (!($obj instanceof jsNumber)) {
            throw new jsException(new jsTypeError());
        }
        $x = $obj->toNumber()->value;

        if (is_nan($x)) {
            return Runtime::js_str("NaN");
        }
        if ($x == 0) {
            return Runtime::js_str("0");
        }
        if ($x < 0 and is_infinite($x)) {
            return Runtime::js_str("-Infinity");
        }
        if (is_infinite($x)) {
            return Runtime::js_str("Infinity");
        }

        $radix = ($radix == Runtime::$undefined) ? 10 : $radix->toNumber()->value;
        if ($radix < 2 || $radix > 36) {
            $radix = 10;
        }
        $v = base_convert($x, 10, $radix);
        if ($x < 0 and $v[0] != '-') {
            $v = "-" . $v;
        }
        return Runtime::js_str($v);
    }

    static public function toPrecision($digits)
    {
        $obj = Runtime::this();
        if ($digits == Runtime::$undefined) {
            return jsNumber::toString($digits);
        }
        $f = $digits->toInteger()->value;
        if ($f < 1 || $f > 21) {
            throw js_exception(js_rangeerror());
        }
        $x = $obj->toNumber()->value;
        if (is_nan($x)) {
            return Runtime::js_str("NaN");
        }
        if (is_infinite($x)) {
            return jsNumber::toString();
        }
        if ($x > ("1e$f" - 0) || $x < 1e-6) {
            return Runtime::js_str(sprintf("%." . $f . "e", $x));
        }
        // not correct. we should count the total number of digits, but yeah, blah.
        return jsNumber::toFixed($digits);
    }

    static public function toFixed($digits)
    {
        $obj = Runtime::this();
        $f = $digits->toInteger()->value;
        if ($f < 0 || $f > 20) {
            throw js_exception(js_rangeerror());
        }
        $x = $obj->toNumber()->value;
        if (is_nan($x)) {
            return Runtime::js_str("NaN");
        }
        if (is_infinite($x)) {
            return jsNumber::toString();
        }
        //return Runtime::js_str(number_format($x, $f));
        // el cheapo version
        $s = strval($x);
        if (strpos($s, ".") === false) {
            return Runtime::js_str($s . "." . str_repeat("0", $digits));
        }
        $k = explode(".", $s);
        if ($f > strlen($k[1])) {
            return Runtime::js_str($k[0] . "." . $k[1] . str_repeat("0", $f - strlen($k[1])));
        } else {
            return Runtime::js_str($k[0] . "." . substr($k[1], 0, $f));
        }
    }

    function defaultValue($iggy = null)
    {
        return $this->value;
    }
}

?>