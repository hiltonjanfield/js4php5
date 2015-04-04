<?php

namespace js4php5\runtime;


class jsRegexp extends jsObject
{

    public $pattern;

    public $flags;

    function __construct($pattern = null, $flags = null)
    {
        parent::__construct("RegExp", Runtime::$proto_regexp);
        $this->pattern = $pattern;
        $this->flags = $flags;
        $this->put("global", (strchr($flags, "g") !== false) ? Runtime::$true : Runtime::$false,
            array("dontdelete", "readonly", "dontenum"));
        $this->put("ignoreCase", (strchr($flags, "i") !== false) ? Runtime::$true : Runtime::$false,
            array("dontdelete", "readonly", "dontenum"));
        $this->put("multiline", (strchr($flags, "m") !== false) ? Runtime::$true : Runtime::$false,
            array("dontdelete", "readonly", "dontenum"));
        $this->put("source", Runtime::js_str($pattern), array("dontdelete", "readonly", "dontenum"));
        $this->put("lastIndex", Runtime::$zero, array("dontdelete", "dontenum"));
    }

////////////////////////
// scriptable methods //
////////////////////////
    static function object($value)
    {
        list ($pattern, $flags) = func_get_args();
        if (!jsFunction::isConstructor() and ($pattern instanceof jsRegexp) and $flags == Runtime::$undefined) {
            return $pattern;
        }
        if ($pattern instanceof jsRegexp) {
            if ($flags != Runtime::$undefined) {
                throw new jsException(new jsTypeError());
            }
            $flags = $pattern->flags;
            $pattern = $pattern->pattern;
        } else {
            $flags = ($flags == Runtime::$undefined) ? "" : $flags->toStr()->value;
            $pattern = ($pattern == Runtime::$undefined) ? "" : $pattern->toStr()->value;
        }
        return new jsRegexp($pattern, $flags);
    }

    static function test($str)
    {
        return (jsRegexp::exec($str) != null) ? Runtime::$true : Runtime::$false;
    }

    static function exec($str)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsRegexp)) {
            throw new jsException(new jsTypeError());
        }
        $s = $str->toStr()->value;
        $len = strlen($s);
        $lastIndex = $obj->get("lastIndex")->toInteger()->value;
        $i = $lastIndex;
        if ($obj->get("global")->toBoolean()->value == false) {
            $i = 0;
        }
        do {
            if ($i < 0 or $i > $len) {
                $obj->put("lastIndex", Runtime::$zero);
                return Runtime::$null;
            }
            $r = $obj->match($s, $i); // XXX write jsRegexp::match()
            $i++;
        } while ($r == null);
        $e = $r["endIndex"];
        $n = $r["length"];
        if ($obj->get("global")->toBoolean()->value == true) {
            $obj->put("lastIndex", Runtime::js_int($e));
        }
        $array = new jsArray();
        $array->put("index", Runtime::js_int($i - 1));
        $array->put("input", $str);
        $array->put("length", $n + 1);
        $array->put(0, Runtime::js_str(substr($s, $i - 1, $e - $i)));
        for ($i = 0; $i < $n; $i++) {
            $array->put($i + 1, Runtime::js_str($r[$i]));
        }
        return $array;
    }

    static function toString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsRegexp)) {
            throw new jsException(new jsTypeError());
        }
        $s = "/" . str_replace(array("/", "\\"), array("\/", "\\\\"), $obj->pattern) . "/";
        if ($obj->get("global") == Runtime::$true) {
            $s .= "g";
        }
        if ($obj->get("ignoreCase") == Runtime::$true) {
            $s .= "i";
        }
        if ($obj->get("multiline") == Runtime::$true) {
            $s .= "m";
        }
        return Runtime::js_str($s);
    }

}

?>