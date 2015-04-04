<?php

namespace js4php5\runtime;

class Runtime
{

    /** @var Object The global Runtime instance. */
    static $global;

    /** @var jsContext[] Holds an array of contexts. */
    static $contexts;

    /** @var jsFunction[] Holds an array of defined functions, including lambda functions. */
    static $functions;

    /** @var Base Variable used like a constant for Zero values. Do not change. */
    static $zero;

    /** @var Base Variable used like a constant for One values. Do not change. */
    static $one;

    /** @var Base Variable used like a constant for True values. Do not change. */
    static $true;

    /** @var Base Variable used like a constant for False values. Do not change. */
    static $false;

    /** @var Base Variable used like a constant for Null values. Do not change. */
    static $null;

    /** @var Base Variable used like a constant for Undefined values. Do not change. */
    static $undefined;

    /** @var Base Variable used like a constant for One values. Do not change. */
    static $nan;

    /** @var Base Variable used like a constant for One. Do not change. */
    static $infinity;

    //TODO: Is this the right variable type? Test exceptions.
    /** @var jsException Holds the current unprocessed exception. */
    static $exception;

    /** @var jsFunction Holds the current sorting function used by jsArray::sort */
    static $sortfn;

    /** @var Object */
    static $proto_object;

    /** @var jsFunction */
    static $proto_function;

    /** @var  jsArray */
    static $proto_array;

    /** @var  jsString */
    static $proto_string;

    /** @var  jsBoolean */
    static $proto_boolean;

    /** @var  jsNumber */
    static $proto_number;

    /** @var  jsDate */
    static $proto_date;

    /** @var  jsRegexp */
    static $proto_regexp;

    /** @var  jsError */
    static $proto_error;

    /** @var  jsEvalError */
    static $proto_evalerror;

    /** @var  jsRangeError */
    static $proto_rangeerror;

    /** @var  jsReferenceError */
    static $proto_referenceerror;

    /** @var  jsSyntaxError */
    static $proto_syntaxerror;

    /** @var  jsTypeError */
    static $proto_typeerror;

    /** @var  jsUriError */
    static $proto_urierror;

    static $idcache;

    static function start_once()
    {
        if (!(Runtime::$global instanceof Object)) {
            Runtime::start();
        }
    }

    static function start()
    {
        // Create a global object.
        Runtime::$global = new jsObject();

        // Create the first execution context.
        Runtime::$contexts = array(new jsContext(Runtime::$global, array(Runtime::$global), Runtime::$global));

        // Set a few basics.
        Runtime::$functions = array();
        Runtime::$nan = self::js_int(acos(1.01));
        Runtime::$infinity = self::js_int(-log(0));
        Runtime::$undefined = new Base(Base::UNDEFINED, 0);
        Runtime::$null = new Base(Base::NULL, 0);
        Runtime::$true = new Base(Base::BOOLEAN, true);
        Runtime::$false = new Base(Base::BOOLEAN, false);
        Runtime::$zero = self::js_int(0);
        Runtime::$one = self::js_int(1);
        Runtime::$exception = null;
        Runtime::$sortfn = null;

        $internal = array("dontenum", "dontdelete", "readonly");

        // Set up prototypes
        Runtime::$proto_object = new jsObject();
        Runtime::push_context(Runtime::$proto_object);
        Runtime::define_function(array("Object", "toString"), 'toString');
        Runtime::define_function(array("Object", "toString"), 'toLocaleString');
        Runtime::define_function(array("Object", "valueOf"), 'valueOf');
        Runtime::define_function(array("Object", "hasOwnProperty"), "hasOwnProperty", array("value"));
        Runtime::define_function(array("Object", "isPrototypeOf"), "isPrototypeOf", array("value"));
        Runtime::define_function(array("Object", "propertyIsEnumerable"), "propertyIsEnumerable", array("value"));
        Runtime::pop_context();
        Runtime::$proto_function = new jsFunction();
        Runtime::push_context(Runtime::$proto_function);
        Runtime::define_function(array("jsFunction", "func_toString"), 'toString');
        Runtime::define_function(array("jsFunction", "func_apply"), 'apply', array("thisArg", "argArray"));
        Runtime::define_function(array("jsFunction", "func_call"), 'call', array("thisArg"));
        Runtime::pop_context();
        Runtime::$proto_array = new jsArray();
        Runtime::push_context(Runtime::$proto_array);
        Runtime::define_function(array("jsArray", "toString"), 'toString');
        Runtime::define_function(array("jsArray", "toLocaleString"), 'toLocaleString');
        Runtime::define_function(array("jsArray", "concat"), "concat", array("item"));
        Runtime::define_function(array("jsArray", "join"), "join", array("separator"));
        Runtime::define_function(array("jsArray", "pop"), "pop");
        Runtime::define_function(array("jsArray", "push"), "push", array("item"));
        Runtime::define_function(array("jsArray", "reverse"), "reverse");
        Runtime::define_function(array("jsArray", "shift"), "shift");
        Runtime::define_function(array("jsArray", "slice"), "slice", array("start", "end"));
        Runtime::define_function(array("jsArray", "sort"), "sort", array("comparefn"));
        Runtime::define_function(array("jsArray", "splice"), "splice", array("start", "deleteCount"));
        Runtime::define_function(array("jsArray", "unshift"), "unshift", array("item"));
        Runtime::pop_context();
        Runtime::$proto_string = new jsString();
        Runtime::push_context(Runtime::$proto_string);
        Runtime::define_function(array("jsString", "toString"), 'toString');
        Runtime::define_function(array("jsString", "toString"), 'valueOf');
        Runtime::define_function(array("jsString", "charAt"), 'charAt', array("pos"));
        Runtime::define_function(array("jsString", "charCodeAt"), 'charCodeAt', array("pos"));
        Runtime::define_function(array("jsString", "concat"), 'concat', array("string"));
        Runtime::define_function(array("jsString", "indexOf"), 'indexOf', array("searchString"));
        Runtime::define_function(array("jsString", "lastIndexOf"), 'lastIndexOf', array("searchString"));
        Runtime::define_function(array("jsString", "localeCompare"), 'localeCompare', array("that"));
        Runtime::define_function(array("jsString", "match"), 'match', array("regexp"));
        Runtime::define_function(array("jsString", "replace"), 'replace', array("searchValue", "replaceValue"));
        Runtime::define_function(array("jsString", "search"), 'search', array("regexp"));
        Runtime::define_function(array("jsString", "slice"), 'slice', array("start", "end"));
        Runtime::define_function(array("jsString", "split"), 'split', array("separator", "limit"));
        Runtime::define_function(array("jsString", "substr"), 'substr', array("start", "length"));
        Runtime::define_function(array("jsString", "substring"), 'substring', array("start", "end"));
        Runtime::define_function(array("jsString", "toLowerCase"), 'toLowerCase');
        Runtime::define_function(array("jsString", "toLocaleLowerCase"), 'toLocaleLowerCase');
        Runtime::define_function(array("jsString", "toUpperCase"), 'toUpperCase');
        Runtime::define_function(array("jsString", "toLocaleUpperCase"), 'toLocaleUpperCase');
        Runtime::pop_context();
        Runtime::$proto_boolean = new jsBoolean();
        Runtime::push_context(Runtime::$proto_boolean);
        Runtime::define_function(array("jsBoolean", "toString"), 'toString');
        Runtime::define_function(array("jsBoolean", "valueOf"), 'valueOf');
        Runtime::pop_context();
        Runtime::$proto_number = new jsNumber();
        Runtime::push_context(Runtime::$proto_number);
        Runtime::define_function(array("jsNumber", "toString"), 'toString', array("radix"));
        Runtime::define_function(array("jsNumber", "toString"), 'toLocaleString', array("radix"));
        Runtime::define_function(array("jsNumber", "valueOf"), 'valueOf');
        Runtime::define_function(array("jsNumber", "toFixed"), 'toFixed', array("fractionDigits"));
        Runtime::define_function(array("jsNumber", "toExponential"), 'toExponential', array("fractionDigits"));
        Runtime::define_function(array("jsNumber", "toPrecision"), 'toPrecision', array("precision"));
        Runtime::pop_context();
        Runtime::$proto_date = new jsDate();
        Runtime::push_context(Runtime::$proto_date);
        Runtime::define_function(array("jsDate", "toString"), 'toString');
        Runtime::define_function(array("jsDate", "toDateString"), 'toDateString');
        Runtime::define_function(array("jsDate", "toTimeString"), 'toTimeString');
        Runtime::define_function(array("jsDate", "toLocaleString"), 'toLocaleString');
        Runtime::define_function(array("jsDate", "toLocaleDateString"), 'toLocaleDateString');
        Runtime::define_function(array("jsDate", "toLocaleTimeString"), 'toLocaleTimeString');
        Runtime::define_function(array("jsDate", "valueOf"), 'valueOf');
        Runtime::define_function(array("jsDate", "getTime"), 'getTime');
        Runtime::define_function(array("jsDate", "getFullYear"), 'getFullYear');
        Runtime::define_function(array("jsDate", "getUTCFullYear"), 'getUTCFullYear');
        Runtime::define_function(array("jsDate", "getMonth"), 'getMonth');
        Runtime::define_function(array("jsDate", "getUTCMonth"), 'getUTCMonth');
        Runtime::define_function(array("jsDate", "getDate"), 'getDate');
        Runtime::define_function(array("jsDate", "getUTCDate"), 'getUTCDate');
        Runtime::define_function(array("jsDate", "getDay"), 'getDay');
        Runtime::define_function(array("jsDate", "getUTCDay"), 'getUTCDay');
        Runtime::define_function(array("jsDate", "getHours"), 'getHours');
        Runtime::define_function(array("jsDate", "getUTCHours"), 'getUTCHours');
        Runtime::define_function(array("jsDate", "getMinutes"), 'getMinutes');
        Runtime::define_function(array("jsDate", "getUTCMinutes"), 'getUTCMinutes');
        Runtime::define_function(array("jsDate", "getSeconds"), 'getSeconds');
        Runtime::define_function(array("jsDate", "getUTCSeconds"), 'getUTCSeconds');
        Runtime::define_function(array("jsDate", "getMilliseconds"), 'getMilliseconds');
        Runtime::define_function(array("jsDate", "getUTCMilliseconds"), 'getUTCMilliseconds');
        Runtime::define_function(array("jsDate", "getTimezoneOffset"), 'getTimezoneOffset');
        Runtime::define_function(array("jsDate", "setTime"), 'setTime', array("time"));
        Runtime::define_function(array("jsDate", "setMilliseconds"), 'setMilliseconds', array("ms"));
        Runtime::define_function(array("jsDate", "setUTCMilliseconds"), 'setUTCMilliseconds', array("ms"));
        Runtime::define_function(array("jsDate", "setSeconds"), 'setSeconds', array("sec", "ms"));
        Runtime::define_function(array("jsDate", "setUTCSeconds"), 'setUTCSeconds', array("sec", "ms"));
        Runtime::define_function(array("jsDate", "setMinutes"), 'setMinutes', array("min", "sec", "ms"));
        Runtime::define_function(array("jsDate", "setUTCMinutes"), 'setUTCMinutes', array("min", "sec", "ms"));
        Runtime::define_function(array("jsDate", "setHours"), 'setHours', array("hour", "min", "sec", "ms"));
        Runtime::define_function(array("jsDate", "setUTCHours"), 'setUTCHours', array("hour", "min", "sec", "ms"));
        Runtime::define_function(array("jsDate", "setDate"), 'setDate', array("date"));
        Runtime::define_function(array("jsDate", "setUTCDate"), 'setUTCDate', array("date"));
        Runtime::define_function(array("jsDate", "setMonth"), 'setMonth', array("month", "date"));
        Runtime::define_function(array("jsDate", "setUTCMonth"), 'setUTCMonth', array("month", "date"));
        Runtime::define_function(array("jsDate", "setFullYear"), 'setFullYear', array("year", "month", "date"));
        Runtime::define_function(array("jsDate", "setUTCFullYear"), 'setUTCFullYear', array("year", "month", "date"));
        Runtime::define_function(array("jsDate", "toUTCString"), 'toUTCString');
        Runtime::pop_context();
        Runtime::$proto_regexp = new jsRegexp();
        Runtime::push_context(Runtime::$proto_regexp);
        Runtime::define_function(array("jsRegexp", "exec"), 'exec', array("string"));
        Runtime::define_function(array("jsRegexp", "test"), 'test', array("string"));
        Runtime::define_function(array("jsRegexp", "toString"), 'toString');
        Runtime::pop_context();
        Runtime::$proto_error = new jsError();
        Runtime::$proto_error->put("name", self::js_str("Error"));
        Runtime::$proto_error->put("message", self::js_str(""));
        Runtime::push_context(Runtime::$proto_error);
        Runtime::define_function(array("jsError", "toString"), 'toString');
        Runtime::pop_context();
        Runtime::$proto_evalerror = new jsEvalError();
        Runtime::$proto_evalerror->put("name", self::js_str("EvalError"));
        Runtime::$proto_evalerror->put("message", self::js_str(""));
        Runtime::$proto_rangeerror = new jsRangeError();
        Runtime::$proto_rangeerror->put("name", self::js_str("RangeError"));
        Runtime::$proto_rangeerror->put("message", self::js_str(""));
        Runtime::$proto_referenceerror = new jsReferenceError();
        Runtime::$proto_referenceerror->put("name", self::js_str("ReferenceError"));
        Runtime::$proto_referenceerror->put("message", self::js_str(""));
        Runtime::$proto_syntaxerror = new jsSyntaxError();
        Runtime::$proto_syntaxerror->put("name", self::js_str("SyntaxError"));
        Runtime::$proto_syntaxerror->put("message", self::js_str(""));
        Runtime::$proto_typeerror = new jsTypeError();
        Runtime::$proto_typeerror->put("name", self::js_str("TypeError"));
        Runtime::$proto_typeerror->put("message", self::js_str(""));
        Runtime::$proto_urierror = new jsUriError();
        Runtime::$proto_urierror->put("name", self::js_str("URIError"));
        Runtime::$proto_urierror->put("message", self::js_str(""));
        #-- populate standard objects
        Runtime::define_variable("NaN", Runtime::$nan);
        Runtime::define_variable("Infinity", Runtime::$infinity);
        Runtime::define_variable("undefined", Runtime::$undefined);

        Runtime::define_function("jsi_eval", "eval");
        Runtime::define_function("jsi_parseInt", "parseInt", array("str", "radix"));
        Runtime::define_function("jsi_parseFloat", "parseFloat", array("str"));
        Runtime::define_function("jsi_isNaN", "isNaN", array("value"));
        Runtime::define_function("jsi_isFinite", "isFinite", array("value"));
        Runtime::define_function("jsi_decodeURI", "decodeURI");
        Runtime::define_function("jsi_decodeURIComponent", "decodeURIComponent");
        Runtime::define_function("jsi_encodeURI", "encodeURI");
        Runtime::define_function("jsi_encodeURIComponent", "encodeURIComponent");

        $o = Runtime::define_function(array("Object", "object"), "Object", array("value"), Runtime::$proto_object);
        Runtime::$proto_object->put("constructor", $o);
        $o = Runtime::define_function(array("jsFunction", "func_object"), "Function", array("value"),
            Runtime::$proto_function);
        Runtime::$proto_function->put("constructor", $o);
        $o = Runtime::define_function(array("jsArray", "object"), "Array", array("value"), Runtime::$proto_array);
        Runtime::$proto_array->put("constructor", $o);
        $o = Runtime::define_function(array("jsString", "object"), "String", array("value"), Runtime::$proto_string);
        Runtime::push_context($o);
        Runtime::define_function(array("jsString", "fromCharCode"), "fromCharCode", array("char"));
        Runtime::pop_context();
        Runtime::$proto_string->put("constructor", $o);
        $o = Runtime::define_function(array("jsBoolean", "object"), "Boolean", array("value"),
            Runtime::$proto_boolean);
        Runtime::$proto_boolean->put("constructor", $o);
        $o = Runtime::define_function(array("jsNumber", "object"), "Number", array("value"), Runtime::$proto_number);
        $o->put("MAX_VALUE", self::js_int(1.7976931348623157e308), $internal);
        $o->put("MIN_VALUE", self::js_int(5e-324), $internal);
        $o->put("NaN", Runtime::$nan, $internal);
        $o->put("NEGATIVE_INFINITY", Runtime::expr_u_minus(Runtime::$infinity), $internal);
        $o->put("POSITIVE_INFINITY", Runtime::$infinity, $internal);
        Runtime::$proto_number->put("constructor", $o);
        $o = Runtime::define_function(array("jsDate", "object"), "Date",
            array("year", "month", "date", "hours", "minutes", "seconds", "ms"), Runtime::$proto_date);
        Runtime::push_context($o);
        Runtime::define_function(array("jsDate", "parse"), "parse", array("string"));
        Runtime::define_function(array("jsDate", "UTC"), "UTC",
            array("year", "month", "date", "hours", "minutes", "seconds", "ms"));
        Runtime::pop_context();
        Runtime::$proto_date->put("constructor", $o);
        $o = Runtime::define_function(array("jsRegexp", "object"), "RegExp", array("pattern", "flags"),
            Runtime::$proto_regexp);
        Runtime::$proto_regexp->put("constructor", $o);
        $o = Runtime::define_function(array("jsError", "object"), "Error", array("message"), Runtime::$proto_error);
        Runtime::$proto_error->put("constructor", $o);
        $o = Runtime::define_function(array("jsEvalError", "object"), "EvalError", array("message"),
            Runtime::$proto_evalerror);
        Runtime::$proto_evalerror->put("constructor", $o);
        $o = Runtime::define_function(array("jsRangeError", "object"), "RangeError", array("message"),
            Runtime::$proto_rangeerror);
        Runtime::$proto_rangeerror->put("constructor", $o);
        $o = Runtime::define_function(array("jsReferenceError", "object"), "ReferenceError", array("message"),
            Runtime::$proto_referenceerror);
        Runtime::$proto_referenceerror->put("constructor", $o);
        $o = Runtime::define_function(array("jsSyntaxError", "object"), "SyntaxError", array("message"),
            Runtime::$proto_syntaxerror);
        Runtime::$proto_syntaxerror->put("constructor", $o);
        $o = Runtime::define_function(array("jsTypeError", "object"), "TypeError", array("message"),
            Runtime::$proto_typeerror);
        Runtime::$proto_typeerror->put("constructor", $o);
        $o = Runtime::define_function(array("jsUriError", "object"), "URIError", array("message"),
            Runtime::$proto_urierror);
        Runtime::$proto_urierror->put("constructor", $o);
        $math = new jsMath();
        Runtime::define_variable("Math", $math);
        $math->put("E", self::js_int(M_E), $internal);
        $math->put("LN10", self::js_int(M_LN10), $internal);
        $math->put("LN2", self::js_int(M_LN2), $internal);
        $math->put("LOG2E", self::js_int(M_LOG2E), $internal);
        $math->put("LOG10E", self::js_int(M_LOG10E), $internal);
        $math->put("PI", self::js_int(M_PI), $internal);
        $math->put("SQRT1_2", self::js_int(M_SQRT1_2), $internal);
        $math->put("SQRT2", self::js_int(M_SQRT2), $internal);
        Runtime::push_context($math);
        Runtime::define_function(array("jsMath", "abs"), "abs", array("x"));
        Runtime::define_function(array("jsMath", "acos"), "acos", array("x"));
        Runtime::define_function(array("jsMath", "asin"), "asin", array("x"));
        Runtime::define_function(array("jsMath", "atan"), "atan", array("x"));
        Runtime::define_function(array("jsMath", "atan2"), "atan2", array("y", "x"));
        Runtime::define_function(array("jsMath", "ceil"), "ceil", array("x"));
        Runtime::define_function(array("jsMath", "cos"), "cos", array("x"));
        Runtime::define_function(array("jsMath", "exp"), "exp", array("x"));
        Runtime::define_function(array("jsMath", "floor"), "floor", array("x"));
        Runtime::define_function(array("jsMath", "log"), "log", array("x"));
        Runtime::define_function(array("jsMath", "max"), "max", array("value1", "value2"));
        Runtime::define_function(array("jsMath", "min"), "min", array("value1", "value2"));
        Runtime::define_function(array("jsMath", "pow"), "pow", array("x", "y"));
        Runtime::define_function(array("jsMath", "random"), "random");
        Runtime::define_function(array("jsMath", "round"), "round", array("x"));
        Runtime::define_function(array("jsMath", "sin"), "sin", array("x"));
        Runtime::define_function(array("jsMath", "sqrt"), "sqrt", array("x"));
        Runtime::define_function(array("jsMath", "tan"), "tan", array("x"));
        Runtime::pop_context();
        // extensions to the spec.
        Runtime::define_variable("global", Runtime::$global);
        Runtime::define_function(array("Runtime", "write"), "write");
        Runtime::define_function(array("Runtime", "write"), "print");
    }

    static function push_context($obj)
    {
        array_unshift(Runtime::$contexts,
            new jsContext(Runtime::$contexts[0]->js_this, Runtime::$contexts[0]->scope_chain, $obj));
    }

    static function define_function($phpname, $jsname = '', $args = array(), $proto = null)
    {
        $func = new jsFunction($jsname, $phpname, $args, Runtime::$contexts[0]->scope_chain);
        if ($proto != null) {
            $func->put("prototype", $proto, array("dontenum", "dontdelete", "readonly"));
        }
        Runtime::$contexts[0]->var->put($jsname, $func);
        if (is_string($phpname)) {
            Runtime::$functions[$phpname] = $func;
        }
        return $func;
    }

    static function pop_context()
    {
        array_shift(Runtime::$contexts);
    }

    static function define_variable($name, $val = null)
    {
        if ($val == null) {
            $val = Runtime::$undefined;
        }
        Runtime::$contexts[0]->var->put($name, $val);
    }

    static function expr_u_minus($a)
    {
        $v = $a->toNumber();
        if (!is_nan($v->value)) {
            $v = self::js_int(-$v->value);
        }
        return $v;
    }

    static function define_variables()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            Runtime::define_variable($arg);
        }
    }

    static function trycatch($expr, $catch, $finally, $id = 0)
    {
        if (js_thrown(Runtime::$exception)) {
            #-- assert($expr == NULL);
            if ($expr != null) {
                echo "TRYCATCH ERROR: INCONSISTENT STATE.<hr><br>";
            }
            /* evaluate catch */
            if ($catch != null) {
                $obj = new jsObject();
                $obj->put($id, Runtime::$exception->value, array("dontdelete"));
                Runtime::$exception = null;
                Runtime::push_scope($obj);
                $ret = $catch();
                Runtime::pop_scope();
                if ($ret != null) {
                    $expr = $ret;
                }
            }
        }
        if ($finally != null) {
            #-- XXX tentative workaround for the call_user_func + exception crash in 5.0.3
            $ret = $finally();
            if ($ret != null) {
                $expr = $ret;
            }
        }
        if (js_thrown(Runtime::$exception)) {
            throw Runtime::$exception; #-- pass it down.
        }
        return $expr;
    }

    static function push_scope($obj)
    {
        array_unshift(Runtime::$contexts[0]->scope_chain, $obj);
        Runtime::$idcache = array();
    }

    static function pop_scope()
    {
        array_shift(Runtime::$contexts[0]->scope_chain);
        Runtime::$idcache = array();
    }

    /* resolve an identifier into a jsRef object */

    static function call($method, $args)
    {
        // not fully compliant with 11.2.3 XXX
        if ($method instanceof jsRef) {
            $that = $method->base;
            if (!$that) {
                //echo '['.get_class($that).'->'.$method->propName.']';
                $that = Runtime::$global;
                $method->base = $that;
            }
            $obj = $method->getValue();
        } else {
            $that = Runtime::$global;
            $obj = $method;
        }
        // ok, call [[Call]]
        if (!$obj) {
            return Runtime::$undefined;
        }
        if ($obj instanceof jsFunction) { // XXX there could be other "callable" objects. maybe.
            Runtime::$idcache = array();
            $ret = $obj->_call($that, $args);
            Runtime::$idcache = array();
            return $ret;
        } else {
            throw new jsException(new jsTypeError("Cannot call an object that isn't a function"));
        }
    }

    static function _new($constructor, $args)
    {
        $c = $constructor; //->getValue();
        if (!($c instanceof jsFunction)) {
            throw new jsException(new jsSyntaxError("invalid constructor"));
        }
        return $c->construct($args);
    }

    static function idv($id)
    {
//        \yii\helpers\VarDumper::dump(Runtime::id($id),4,true);exit;
        return Runtime::id($id)->getValue();
    }

    static function id($id)
    {
        if (!isset(Runtime::$idcache[$id])) {
            #-- get scope chain
            $chain = Runtime::$contexts[0]->scope_chain;
            foreach ($chain as $scope) {
                if ($scope->hasProperty($id)) {
                    /*
                    if (isset(Runtime::$idcache[$id]) and Runtime::$idcache[$id]->base != $scope) {
                    echo "bad cache for $id..<br>";
                    echo "old scope = ".serialize(Runtime::$idcache[$id]->base)."<br>";
                    echo "new scope = ".serialize($scope)."<br>";
                    }
                    */
                    Runtime::$idcache[$id] = new jsRef($scope, $id);
                    return Runtime::$idcache[$id];

                }
            }
            return new jsRefNull($id);
        }
        return Runtime::$idcache[$id];
    }

    static function debug($obj)
    {
        if (is_object($obj)) {
            echo $obj->toDebug();
        } else {
            echo "[NOTANOBJECT=" . $obj . "]";
        }
    }

    static function dotv($base, $prop)
    {
        //echo @"DOTV(base, ".$prop->propName.")<br>";
        return Runtime::dot($base, $prop)->getValue();
    }

    static function dot($base, $prop)
    {
        $obj = $base; //->getValue();
        if ($obj == Runtime::$null) {
            echo "dot(NULL, xxx) DOES NOT COMPUTE. ABORT! <pre>";
            debug_print_backtrace();
            echo "</pre>";
        }
        if (!($prop instanceof jsRef)) {
            $base = $prop->toStr()->value;
        } else {
            $base = $prop->propName;
        }
        // echo "Computing ".get_class($obj->toObject())."->".$base."<br>";
        // Runtime::debug($obj);
        return new jsRef($obj->toObject(), $base);
    }

    static function function_id($phpname)
    {
        if (isset(Runtime::$functions[$phpname])) {
            return Runtime::$functions[$phpname];
        }
        return Runtime::$undefined;
    }

    static function literal_array()
    {
        $args = func_get_args();
        $array = new jsArray();
        foreach ($args as $arg) {
            $array->_push($arg);
        }
        return $array;
    }

    static function literal_object()
    {
        $args = func_get_args();
        $obj = new jsObject();
        for ($i = 0; $i < count($args); $i += 2) {
            $obj->put($args[$i]->value, $args[$i + 1]);
        }
        return $obj;
    }

    static function this()
    {
        $t = Runtime::$contexts[0]->js_this;
        if ($t) {
            return $t;
        }
        return Runtime::$global;
    }

    static function expr_assign($left, $right, $op = null)
    {
        return $left->putValue(($op == null) ? $right : Runtime::$op($left->getValue(), $right), 1);
    }

    static function expr_comma($a, $b)
    {
        return $b;
    }

    static function expr_plus($a, $b)
    {
        $a = $a->toPrimitive();
        $b = $b->toPrimitive();
        if ($a->type == Base::STRING or $b->type == Base::STRING) {
            $a = $a->toStr();
            $b = $b->toStr();
            return self::js_str($a->value . $b->value);
        } else {
            $a = $a->toNumber();
            $b = $b->toNumber();
            return self::js_int($a->value + $b->value);
        }
    }

    static function expr_minus($a, $b)
    {
        return self::js_int($a->toNumber()->value - $b->toNumber()->value);
    }

    static function expr_divide($a, $b)
    {
        $a = $a->toNumber()->value;
        $b = $b->toNumber()->value;
        if (is_nan($a) or is_nan($b)) {
            return Runtime::$nan;
        }
        if (is_infinite($a) and is_infinite($b)) {
            return Runtime::$nan;
        }
        if (is_infinite($a)) {
            return Runtime::$infinity;
        } // wrong sign XXX
        if (is_infinite($b)) {
            return Runtime::$zero;
        }
        if ($a == 0 and $b == 0) {
            return Runtime::$nan;
        }
        if ($b == 0) {
            return Runtime::$infinity;
        } // wrong sign. again. XXX
        return @self::js_int($a / $b);
    }

    /**
     * @param Base $a
     * @param Base $b
     *
     * @return Base
     */
    static function expr_multiply($a, $b)
    {
        return self::js_int($a->toNumber()->value * $b->toNumber()->value);
    }

    static function expr_modulo($a, $b)
    {
        return self::js_int($a->toNumber()->value % $b->toNumber()->value);
    }

    static function expr_post_pp($a)
    {
        return $a->putValue(self::js_int($a->getValue()->toNumber()->value + 1), 2);
    }

    static function expr_post_mm($a)
    {
        return $a->putValue(self::js_int($a->getValue()->toNumber()->value - 1), 2);
    }

    static function expr_delete($a)
    {
        if (!($a instanceof jsRef)) {
            return Runtime::$true;
        }
        // clear the idcache
        Runtime::$idcache = array();
        return $a->base->delete($a->propName);
    }

    static function expr_void($a)
    {
        return Runtime::$undefined;
    }

    static function expr_typeof($a)
    {
        if ($a instanceof jsRef) {
            if ($a->base == null) {
                return Runtime::$undefined;
            }
        }
        $a = $a->getValue();
        switch ($a->type) {
            case Base::UNDEFINED:
                return self::js_str("undefined");
            case Base::NULL:
                return self::js_str("object");
            case Base::BOOLEAN:
                return self::js_str("boolean");
            case Base::NUMBER:
                return self::js_str("number");
            case Base::STRING:
                return self::js_str("string");
            case Base::OBJECT:
                if ($a instanceof jsFunction) {
                    return self::js_str("function");
                } else {
                    return self::js_str("object");
                }
        }
        return self::js_str("unknown"); // inspired by IE, or something
    }

    static function expr_pre_pp($a)
    {
        $v = $a->getValue()->toNumber();
        $v = self::js_int($v->value + 1);
        $a->putValue($v);
        return $v;
    }

    static function expr_pre_mm($a)
    {
        $v = $a->getValue()->toNumber();
        $v = self::js_int($v->value - 1);
        $a->putValue($v);
        return $v;
    }

    static function expr_u_plus($a)
    {
        return $a->toNumber();
    }

    static function expr_bit_not($a)
    {
        return self::js_int(~$a->toInt32()->value);
    }

    static function expr_not($a)
    {
        return ($a->toBoolean()->value) ? Runtime::$false : Runtime::$true;
    }

    static function expr_lsh($a, $b)
    {
        $a = $a->toInt32();
        $b = $b->toUInt32();
        $v = self::js_int($a->value << ($b->value & 0x1F));
        return $v;
        // XXX potential problem here. $v may be bigger than 32 bits.
    }

    static function expr_rsh($a, $b)
    {
        return self::js_int($a->toInt32()->value >> ($b->toUInt32()->value & 0x1F));
    }

    static function expr_ursh($a, $b)
    {
        $a = $a->toInt32()->value;
        $b = $b->toUInt32()->value;
        $i = $a >> ($b & 0x1F);
        // now I need to zero the b-th highest bits.
        $k = 0x80000000;
        for ($c = 0; $c < $b; $c++) {
            $i &= ~$k;
            $k >>= 1;
        }
        // pretty freaking slow. XXX think of a faster way.
        return self::js_int($i);
    }

    static function expr_lt($a, $b)
    {
        return Runtime::cmp($a, $b, 1);
    }

    static function cmp($a, $b, $f = 0)
    {
        $a = $a->toPrimitive(Base::NUMBER);
        $b = $b->toPrimitive(Base::NUMBER);
        if ($a->type == Base::STRING and $b->type == Base::STRING) {
            if (strpos($a->value, $b->value) === 0) {
                return Runtime::$false;
            }
            if (strpos($b->value, $a->value) === 0) {
                return Runtime::$true;
            }
            return ($a < $b) ? Runtime::$true : Runtime::$false; // XXX may not be 100% correct with 11.8.5.[18-21]
        } else {
            $a = $a->toNumber();
            $b = $b->toNumber();
            if (is_nan($a->value) or is_nan($b->value)) {
                return $f ? Runtime::$false : Runtime::$undefined;
            }
            if ($a->value == $b->value) {
                return Runtime::$false;
            }
            /*
            if ($a->value>0 and is_infinite($a->value)) return Runtime::$false;
            if ($b->value>0 and is_infinite($b->value)) return Runtime::$true;
            if ($b->value<0 and is_infinite($b->value)) return Runtime::$false;
            if ($a->value<0 and is_infinite($a->value)) return Runtime::$true;
            */
            return ($a->value < $b->value) ? Runtime::$true : Runtime::$false; // XXX 11.8.5.15
        }
    }

    static function expr_gt($a, $b)
    {
        return Runtime::cmp($b, $a, 1);
    }

    static function expr_lte($a, $b)
    {
        $v = Runtime::cmp($b, $a);
        if ($v == Runtime::$true or $v == Runtime::$undefined) {
            return Runtime::$false;
        }
        return $v;
    }

    static function expr_gte($a, $b)
    {
        $v = Runtime::cmp($a, $b);
        if ($v == Runtime::$true or $v == Runtime::$undefined) {
            return Runtime::$false;
        }
        return $v;
    }

    static function expr_instanceof($a, $b)
    {
        if ($b->type != Base::OBJECT) {
            echo "ERROR: TypeError Exception at line " . __LINE__ . " in file " . __FILE__ . "<hr>";
            return Runtime::$undefined;
        }
        return $b->hasInstance($a);
    }

    static function expr_in($a, $b)
    {
        if ($b->type != Base::OBJECT) {
            echo "ERROR: TypeError Exception at line " . __LINE__ . " in file " . __FILE__ . "<hr>";
            return Runtime::$undefined;
        }
        $a = $a->toStr();
        return $b->hasProperty($a);
    }

    static function expr_equal($a, $b)
    {
        return Runtime::abstract_equal($a, $b);
    }

    static function abstract_equal($a, $b)
    {
        if ($a->type != $b->type) {
            if ($a->type == Base::UNDEFINED and $b->type == Base::NULL) {
                return Runtime::$true;
            }
            if ($b->type == Base::UNDEFINED and $a->type == Base::NULL) {
                return Runtime::$true;
            }
            if ($a->type == Base::NUMBER and $b->type == Base::STRING) {
                return Runtime::abstract_equal($a, $b->toNumber());
            }
            if ($b->type == Base::NUMBER and $a->type == Base::STRING) {
                return Runtime::abstract_equal($a->toNumber(), $b);
            }
            if ($a->type == Base::BOOLEAN) {
                return Runtime::abstract_equal($a->toNumber(), $b);
            }
            if ($b->type == Base::BOOLEAN) {
                return Runtime::abstract_equal($a, $b->toNumber());
            }
            if (($a->type == Base::NUMBER or $a->type == Base::STRING) and $b->type == Base::OBJECT) {
                return Runtime::abstract_equal($a, $b->toPrimitive());
            }
            if (($b->type == Base::NUMBER or $b->type == Base::STRING) and $a->type == Base::OBJECT) {
                return Runtime::abstract_equal($a->toPrimitive(), $b);
            }
            return Runtime::$false;
        } else {
            if ($a->type == Base::UNDEFINED) {
                return Runtime::$true;
            }
            if ($a->type == Base::NULL) {
                return Runtime::$true;
            }
            if ($a->type == Base::NUMBER) {
                if (is_nan($a->value) or is_nan($b->value)) {
                    return Runtime::$false;
                }
            }
            if ($a->type == Base::OBJECT) {
                return ($a === $b) ? Runtime::$true : Runtime::$false;
            }
            return ($a->value == $b->value) ? Runtime::$true : Runtime::$false;
        }
    }

    static function expr_not_equal($a, $b)
    {
        return Runtime::abstract_equal($a, $b)->value ? Runtime::$false : Runtime::$true;
    }

    static function expr_strict_equal($a, $b)
    {
        $v = Runtime::strict_equal($a, $b);
        return $v;
    }

    static function strict_equal($a, $b)
    {
        if ($a->type != $b->type) {
            return Runtime::$false;
        }
        if ($a->type == Base::UNDEFINED or $a->type == Base::NULL) {
            return Runtime::$true;
        }
        if ($a->type == Base::NUMBER) {
            if (is_nan($a->value) or is_nan($b->value)) {
                return Runtime::$false;
            }
        }
        if ($a->type == Base::OBJECT) {
            return ($a === $b) ? Runtime::$true : Runtime::$false;
        }
        return ($a->value == $b->value) ? Runtime::$true : Runtime::$false;
    }

    static function expr_strict_not_equal($a, $b)
    {
        return Runtime::strict_equal($a, $b)->value ? Runtime::$false : Runtime::$true;
    }

    static function expr_bit_and($a, $b)
    {
        return self::js_int($a->toInt32()->value & $b->toInt32()->value);
    }

    static function expr_bit_xor($a, $b)
    {
        return self::js_int($a->toInt32()->value ^ $b->toInt32()->value);
    }

    static function expr_bit_or($a, $b)
    {
        return self::js_int($a->toInt32()->value | $b->toInt32()->value);
    }

    static function write()
    {
        $args = func_get_args();
        foreach ($args as $arg) {
            $s = $arg->toStr();
            echo $s->value;
        }
        //ob_flush();
        flush();
    }

    static public function js_str($s)
    {
        static $cache = array();
        if (!isset($cache[$s])) {
            $cache[$s] = new Base(Base::STRING, $s);
        }
        return $cache[$s];
    }

    static public function js_int($i)
    {
        static $cache = array();
        $s = strval($i);
        if (!isset($cache[$s])) {
            $cache[$s] = new Base(Base::NUMBER, $i);
        }
        //echo "js_int($i) = ".serialize($cache[$s])."<br>";
        return $cache[$s];
    }

    static public function js_bool($v)
    {
        return $v->toBoolean()->value;
    }

    static public function js_obj($v)
    {
        return $v->toObject();
    }

    static public function js_thrown($v)
    {
        return (get_class($v) == "jsException" and $v->type == jsException::EXCEPTION);
    }

    /**
     * @param Base $o
     *
     * @return int
     */
    static public function php_int($o)
    {
        return $o->toNumber()->value;
    }

    /**
     * @param Base $o
     *
     * @return string
     */
    static public function php_str($o)
    {
        return $o->toStr()->value;
    }

    /**
     * @param Base $o
     *
     * @return bool
     */
    static public function php_bool($o)
    {
        return $o->toBoolean()->value;
    }
}
