<?php

namespace js4php5\runtime;


class jsArray extends jsObject
{

    protected $length;

    function __construct($len = 0, $args = array())
    {
        parent::__construct("Array", Runtime::$proto_array);
        if ($len == 0) {
            $len = Runtime::$zero;
        }
        $this->length = $len;
        foreach ($args as $index => $value) {
            echo "Setting $index to $value<br>";
            $this->put($index, $value);
        }
    }

    function put($name, $value, $opts = null)
    {
        $name = strval($name);
        //echo "Setting $name to ".serialize($value)."<br>";
        if ($name == "length") {
            //$value = $value->getValue();
            if ($value->value < $this->length->value) {
                #-- truncate.
                foreach ($this->slots as $index => $value) {
                    if (is_numeric($index) and $index >= $value->value) {
                        $this->delete($index);
                    }
                }
            }
            $this->length = $value;
        } else {
            if (is_numeric($name)) {
                if ($name >= $this->length) {
                    $this->length = Runtime::js_int($name + 1);
                }
            }
            return parent::put($name, $value, $opts);
        }
    }

    static public function object($value)
    {
        if (func_num_args() == 1 and $value->type == Base::NUMBER and $value->toUInt32()->value == $value->value) {
            $obj = new jsArray();
            $obj->put("length", $value);
            return $obj;
        }
        $contrived = func_get_args();
        return call_user_func_array(array("Runtime", "literal_array"), $contrived);
    }

    static public function toLocaleString()
    {
        // XXX write a localized version
        return jsArray::toString();
    }

    static public function toString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsArray)) {
            throw new jsException(new jsTypeError());
        }
        return $obj->defaultValue();
    }

    static public function concat()
    {
        $array = new jsArray();
        $args = func_get_args();
        array_unshift($args, Runtime::$this());
        while (count($args) > 0) {
            $obj = array_shift($args);
            if (!($obj instanceof jsArray)) {
                $array->_push($obj);
            } else {
                $len = $obj->get("length")->value;
                for ($k = 0; $k < $len; $k++) {
                    if ($obj->hasProperty($k)) {
                        $array->_push($obj->get($k));
                    }
                }
            }
        }
        return $array;
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////

    function _push($val)
    {
        $v = $this->length->value;
        $this->put($v, $val);
        //$this->length = Runtime::js_int($v+1);
    }

    static public function join($sep)
    {
        $obj = Runtime::this();
        $len = $obj->get("length")->toUInt32()->value;
        if ($sep == Runtime::$undefined) {
            $sep = ",";
        } else {
            $sep = $sep->toStr()->value;
        }
        if ($len == 0) {
            return Runtime::js_str("");
        }
        $arr = jsArray::toNativeArray($obj);
        $arr2 = array();
        foreach ($arr as $elem) {
            array_push($arr->toStr());
        }
        return Runtime::js_str(implode($sep, $arr2));
    }

    static function toNativeArray($obj)
    {
        $len = $obj->get("length")->value;
        $arr = array();
        for ($i = 0; $i < $len; $i++) {
            $arr[$i] = $obj->get($i);
        }
        return $arr;
    }

    static public function pop()
    {
        $obj = Runtime::this();
        $len = $obj->get("length")->toUInt32();
        if ($len->value == 0) {
            $obj->put("lengh", $len);
            return Runtime::$undefined;
        }
        $index = $len->value - 1;
        $elem = $obj->get($index);
        $obj->delete($index);
        $obj->put("length", Runtime::js_int($index));
        return $elem;
    }

    static public function push()
    {
        $obj = Runtime::this();
        $n = $obj->get("length")->toUInt32()->value;
        $args = func_get_Args();
        while (count($args) > 0) {
            $arg = array_shift($args);
            $obj->put($n, $arg);
            $n++;
        }
        $obj->put("length", Runtime::js_int($n));
        return $n;
    }

    static public function reverse()
    {
        $obj = Runtime::this();
        $len = $obj->get("length")->toUInt32()->value;
        $mid = floor($len / 2);
        $k = 0;
        while ($k != $mid) {
            $l = $len - $k - 1;
            if (!$obj->hasProperty($k)) {
                if (!$obj->hasProperty($l)) {
                    $obj->delete($k);
                    $obj->delete($l);
                } else {
                    $obj->put($k, $obj->get($l));
                    $obj->delete($l);
                }
            } else {
                if (!$obj->hasProperty($l)) {
                    $obj->put($l, $obj->get($k));
                    $obj->delete($k);
                } else {
                    $a = $obj->get($k);
                    $obj->put($k, $obj->get($l));
                    $obj->put($l, $a);
                }
            }
            $k++;
        }
        return $obj;
    }

    static public function shift()
    {
        $obj = Runtime::this();
        $len = $obj->get("length")->toUInt32()->value;
        if ($len == 0) {
            $obj->put("length", 0);
            return Runtime::$undefined;
        }
        $first = $obj->get(0);
        $k = 1;
        while ($k != $len) {
            if ($obj->hasProperty($k)) {
                $obj->put($k - 1, $obj->get($k));
            } else {
                $obj->delete($k - 1);
            }
            $k++;
        }
        $obj->delete($len - 1);
        $obj->put("length", $len - 1);
        return $first;
    }

    static public function slice($start, $end)
    {
        $obj = Runtime::this();
        $array = new jsArray();
        $len = $obj->get("length")->toUInt32()->value;
        $start = $start->toInteger()->value;
        $k = ($start < 0) ? max($len + $start, 0) : min($len, $start);
        if ($end == Runtime::$undefined) {
            $end = $len;
        } else {
            $end = $end->toInteger()->value;
        }
        $end = ($end < 0) ? max($len + $end, 0) : min($len, $end);
        $n = 0;
        while ($k < $end) {
            if ($obj->hasProperty($k)) {
                $array->put($n, $obj->get($k));
            }
            $k++;
            $n++;
        }
        $array->put("length", $n);
        return $array;
    }

    static public function sort($comparefn)
    {
        $obj = Runtime::this();
        $arr = jsArray::toNativeArray($obj);

        Runtime::$sortfn = $comparefn;
        usort($arr, array("js_array", "sort_helper"));
        Runtime::$sortfn = null;
        $len = count($arr);
        for ($i = 0; $i < $len; $i++) {
            $obj->put($i, $arr[$i]);
        }
        $obj->put('length', Runtime::js_int($len));
        return $obj;
    }

    static public function sort_helper($a, $b)
    {
        if ($a == Runtime::$undefined) {
            if ($b == Runtime::$undefined) {
                return 0;
            } else {
                return 1;
            }
        } else {
            if ($b == Runtime::$undefined) {
                return -1;
            }
        }
        if (Runtime::$sortfn == null or Runtime::$sortfn == Runtime::$undefined) {
            $a = $a->toStr();
            $b = $b->toStr();
            if (js_bool(Runtime::expr_lt($a, $b))) {
                return -1;
            }
            if (js_bool(Runtime::expr_gt($a, $b))) {
                return 1;
            }
            return 0;
        } else {
            return Runtime::$sortfn->_call($a, $b)->toInteger()->value;
        }
    }

    static public function splice($start, $deleteCount)
    {
        $obj = Runtime::this();
        $args = func_get_args();
        array_shift($args);
        array_shift($args);
        $array = new jsArray();
        $len = $obj->get("length")->toUInt32()->value;
        $start = $start->toInteger();
        $start = ($start < 0) ? max($len + $start, 0) : min($len, $start);
        $count = min(max($deleteCount->toInteger(), 0), $len - $start);
        $k = 0;
        while ($k != $count) {
            if ($obj->hasProperty($start + $k)) {
                $array->put($k, $obj->get($start + $k));
            }
            $k++;
        }
        $array->put("length", Runtime::js_int($count));
        $nbitems = count($args);
        if ($nbitems != $count) {
            if ($nbitems <= $count) {
                $k = $start;
                while ($k != $len - $count) {
                    $r22 = $k + $count;
                    $r23 = $k + $nbitems;
                    if ($obj->hasProperty($r22)) {
                        $obj->put($r23, $obj->get($r22));
                    } else {
                        $obj->delete($r23);
                    }
                    $k++;
                }
                $k = $len;
                while ($k != $len - $count + $nbitems) {
                    $obj->delete($k - 1);
                    $k--;
                }
            } else {
                $k = $len - $count;
                while ($k != $start) {
                    $r39 = $k + $count - 1;
                    $r40 = $k + $nbitems - 1;
                    if ($obj->hasProperty($r39)) {
                        $obj->put($r40, $obj->get($r39));
                    } else {
                        $obj->delete($r40);
                    }
                    $k--;
                }
            }
        }
        $k = $start;
        while (count($args) > 0) {
            $obj->put($k++, array_shift($args));
        }
        $obj->put("length", Runtime::js_int($len - $count + $nbitems));
        return $array;
    }

    static public function unshift()
    {
        $obj = Runtime::this();
        $len = $obj->get("length")->toUInt32()->value;
        $args = func_get_args();
        $nbitems = count($args);
        $k = $len;
        while ($k != 0) {
            if ($obj->hasProperty($k - 1)) {
                $obj->put($k + $nbitems - 1, $obj->get($k - 1));
            } else {
                $obj->delete($k + $nbitems - 1);
            }
            $k--;
        }
        while (count($args) > 0) {
            $obj->put($k, array_shift($args));
            $k++;
        }
        $obj->put("length", $len + $nbitems);
        return Runtime::js_int($len + $nbitems);
    }

    function defaultValue($iggy = null)
    {
        $arr = array();
        for ($i = 0; $i < $this->length->value; $i++) {
            $arr[$i] = '';
        }
        foreach ($this->slots as $index => $value) {
            if (is_numeric($index)) {
                $arr[$index] = $value->value->toStr()->value;
            }
        }
        $o = implode(",", $arr);
        return Runtime::js_str($o);
    }

    function get($name)
    {
        $name = strval($name);
        if ($name == "length") {
            return $this->length;
        } else {
            return parent::get($name);
        }
    }
}

?>