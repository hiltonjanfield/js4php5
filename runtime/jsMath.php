<?php

namespace js4php5\runtime;


class jsMath extends jsObject
{
    function __construct()
    {
        parent::__construct("Math");
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////
    static function abs($x)
    {
        return Runtime::js_int(abs($x->toNumber()->value));
    }

    static function acos($x)
    {
        return Runtime::js_int(acos($x->toNumber()->value));
    }

    static function asin($x)
    {
        return Runtime::js_int(asin($x->toNumber()->value));
    }

    static function atan($x)
    {
        return Runtime::js_int(atan($x->toNumber()->value));
    }

    static function atan2($y, $x)
    {
        return Runtime::js_int(atan2($y->toNumber()->value, $x->toNumber()->value));
    }

    static function ceil($x)
    {
        return Runtime::js_int(ceil($x->toNumber()->value));
    }

    static function cos($x)
    {
        return Runtime::js_int(cos($x->toNumber()->value));
    }

    static function exp($x)
    {
        return Runtime::js_int(exp($x->toNumber()->value));
    }

    static function floor($x)
    {
        return Runtime::js_int(floor($x->toNumber()->value));
    }

    static function log($x)
    {
        return Runtime::js_int(log($x->toNumber()->value));
    }

    static function max($v1, $v2)
    {
        $args = func_get_args();
        if (count($args) == 0) {
            return Runtime::js_int(log(0));
        } //-Infinity
        $arr = array();
        foreach ($args as $arg) {
            $v = $arg->toNumber()->value;
            if (is_nan($v)) {
                return Runtime::$nan;
            }
            $arr[] = $v;
        }
        return Runtime::js_int(max($arr));
    }

    static function min($v1, $v2)
    {
        $args = func_get_args();
        if (count($args) == 0) {
            return Runtime::$infinity;
        }
        $arr = array();
        foreach ($args as $arg) {
            $v = $arg->toNumber()->value;
            if (is_nan($v)) {
                return Runtime::$nan;
            }
            $arr[] = $v;
        }
        return Runtime::js_int(min($arr));
    }

    static function pow($x, $y)
    {
        return Runtime::js_int(pow($x->toNumber()->value, $y->toNumber()->value));
    }

    static function random()
    {
        return Runtime::js_int(mt_rand() / mt_getrandmax());
    }

    static function round($x)
    {
        return Runtime::js_int(round($x->toNumber()->value));
    }

    static function sin($x)
    {
        return Runtime::js_int(sin($x->toNumber()->value));
    }

    static function sqrt($x)
    {
        return Runtime::js_int(sqrt($x->toNumber()->value));
    }

    static function tan($x)
    {
        return Runtime::js_int(tan($x->toNumber()->value));
    }
}

?>