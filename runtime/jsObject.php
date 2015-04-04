<?php

namespace js4php5\runtime;

use Iterator;

class jsObject extends Base implements Iterator
{

    /** @var jsObject|null */
    public $prototype = null;

    /** @var string */
    public $class = "Object";

    /**
     * @param string $class
     * @param null|jsObject $proto
     */
    function __construct($class = "Object", $proto = null)
    {
        parent::__construct(Base::OBJECT, null);
        switch ($class) {
            default: /* default to Object */
            case "Object":
                $this->prototype = Runtime::$proto_object;
                break;
            case "Function":
                $this->prototype = Runtime::$proto_function;
                break;
            case "Array":
                $this->prototype = Runtime::$proto_array;
                break;
            case "String":
                $this->prototype = Runtime::$proto_string;
                break;
            case "Boolean":
                $this->prototype = Runtime::$proto_boolean;
                break;
            case "Number":
                $this->prototype = Runtime::$proto_number;
                break;
            case "Date":
                $this->prototype = Runtime::$proto_date;
                break;
            case "RegExp":
                $this->prototype = Runtime::$proto_regexp;
                break;
            case "Error":
                $this->prototype = Runtime::$proto_error;
                break;
            case "EvalError":
                $this->prototype = Runtime::$proto_evalerror;
                $class = "Error";
                break;
            case "RangeError":
                $this->prototype = Runtime::$proto_rangeerror;
                $class = "Error";
                break;
            case "ReferenceError":
                $this->prototype = Runtime::$proto_referenceerror;
                $class = "Error";
                break;
            case "SyntaxError":
                $this->prototype = Runtime::$proto_syntaxerror;
                $class = "Error";
                break;
            case "TypeError":
                $this->prototype = Runtime::$proto_typeerror;
                $class = "Error";
                break;
            case "URIError":
                $this->prototype = Runtime::$proto_urierror;
                $class = "Error";
                break;
        }
        $this->class = $class;
        $this->prototype = ($proto == null) ? Runtime::$proto_object : $proto;
    }

    static public function object($value)
    {
        if ($value != Runtime::$null and $value != Runtime::$undefined) {
            return $value->toObject();
        }
        #-- back to our regularly scheduled constructor.
        return new jsObject("Object");
    }

    static public function toString()
    {
        $obj = Runtime::this();
        return js_str("[object " . $obj->class . "]");
    }

    static public function valueOf()
    {
        return Runtime::this();
    }

    static public function hasOwnProperty($value)
    {
        $obj = Runtime::this();
        $name = $value->toStr()->value;
        return (isset($obj->slots[$name])) ? Runtime::$true : Runtime::$false;
    }

    static public function isPrototypeOf($value)
    {
        $obj = Runtime::this();
        if ($value->type != Base::OBJECT) {
            return Runtime::$false;
        }
        do {
            $value = $value->prototype;
            if ($value == null) {
                return Runtime::$false;
            }
            if ($obj === $value) {
                return Runtime::$true;
            }
        } while (true);
    }

    static public function propertyIsEnumerable($value)
    {
        $obj = Runtime::this();
        $name = $value->toStr()->value;
        if (!isset($obj->slots[$name])) {
            return Runtime::$false;
        }
        $attr = $obj->slots[$name];
        return !$attr->dontenum;
    }

    function put($name, $value, $opts = null)
    {
        $name = strval($name);
        if (!$this->canPut($name)) {
            return;
        }
        if ($value instanceof jsRef) {
            echo "<pre>";
            debug_print_backtrace();
            echo "</pre>";
        }
        //$value = $value->getValue();
        if (isset($this->slots[$name])) {
            $o = $this->slots[$name];
            $o->value = $value;
        } else {
            $o = new jsAttribute($value);
            $this->slots[$name] = $o;
        }
        if ($opts) {
            foreach ($opts as $opt) {
                $o->$opt = true;
            }
        }
    }
    //////////////////////
    // Iterator interface
    //////////////////////

    function canPut($name)
    {
        $name = strval($name);
        if (isset($this->slots[$name])) {
            return $this->slots[$name]->readonly == false;
        }
        if ($this->prototype == null) {
            return true;
        }
        return $this->prototype->canPut($name);
    }

    function hasProperty($name)
    {
        if (isset($this->slots[strval($name)])) {
            return true;
        }
        if ($this->prototype == null) {
            return false;
        }
        return $this->prototype->hasProperty($name);
    }

    function delete($name)
    {
        $name = strval($name);
        if (!isset($this->slots[$name])) {
            return true;
        }
        if ($this->slots[$name]->dontdelete) {
            return false;
        }
        unset($this->slots[$name]);
        return true;
    }

    function defaultValue($hint = Base::NUMBER)
    {
        switch ($hint) {
            case Base::STRING:
                $v = $this->pcall("toString");
                if ($v != Runtime::$undefined) {
                    return $v;
                }
                $v = $this->pcall("valueOf");
                if ($v != Runtime::$undefined) {
                    return $v;
                }
                break;
            case Base::NUMBER:
                $v = $this->pcall("valueOf");
                if ($v != Runtime::$undefined) {
                    return $v;
                }
                $v = $this->pcall("toString");
                if ($v != Runtime::$undefined) {
                    return $v;
                }
                break;
        }
        // to a toSource(), just because.
        return $this->pcall("toSource");
    }

    protected function pcall($n)
    {
        $p = $this->get($n);
        if ($p->type == Base::OBJECT) {
            $v = $p->_call($this);
            if ($v->type != Base::OBJECT) {
                return $v;
            }
        }
        return Runtime::$undefined;
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////

    function get($name)
    {
        $name = strval($name);
        if (isset($this->slots[$name])) {
            return $this->slots[$name]->value;
        } else {
            if ($this->prototype == null) {
                return Runtime::$undefined;
            }
            return $this->prototype->get($name);
        }
    }

    public function rewind()
    {
        reset($this->slots);
    }

    public function current()
    {
        $attr = current($this->slots);
        return $attr ? key($this->slots) : Runtime::$undefined;
    }

    public function key()
    {
        return key($this->slots);
    }

    public function next()
    {
        do {
            $attr = next($this->slots);
        } while ($attr and $attr->dontenum);
        return $attr ? key($this->slots) : Runtime::$undefined;
    }

    public function valid()
    {
        return (key($this->slots) !== null);
    }
}

