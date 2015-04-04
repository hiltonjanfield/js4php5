<?php

namespace js4php5\runtime;

use hiltonjanfield\js4php5\VarDumper;

class Base
{

//    const UNDEFINED = 0;
//    const NULL = 1;
//    const BOOLEAN = 2;
//    const NUMBER = 3;
//    const STRING = 4;
//    const OBJECT = 5;
//    const REF = 6;
    // Using string constants during development for easier review of object dumps.
    // This has almost *no* impact on speed anyway, with a difference of 0-30ms on 1,000,000 iterations.
    const UNDEFINED = 'undefined';
    const NULL = 'null';
    const BOOLEAN = 'boolean';
    const NUMBER = 'number';
    const STRING = 'string';
    const OBJECT = 'object';
    const REF = 'ref';

    /** @var int|string Type of $value; one of the Base:: constants. */
    public $type;

    /** @var mixed */
    public $value;

    /** @var Base[] Only used if type is Base::OBJECT. */
    public $slots;

    /**
     * @param int|string $type
     * @param mixed $value
     */
    function __construct($type, $value)
    {
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        $x = explode('\\', get_called_class());
        return end($x);
    }

    /**
     * @return Base JavaScript value converted to a boolean using JavaScript rules.
     */
    function toBoolean()
    {
        switch ($this->type) {
            case Base::UNDEFINED:
            case Base::NULL:
                return Runtime::$false;
            case Base::OBJECT:
                return Runtime::$true;
            case Base::BOOLEAN:
                return $this;
            case Base::NUMBER:
                return ($this->value == 0 or is_nan($this->value)) ? Runtime::$false : Runtime::$true;
            case Base::STRING:
                return (strlen($this->value) == 0) ? Runtime::$false : Runtime::$true;
        }
    }

    /**
     * @return Base JavaScript value converted to a 32-bit integer using JavaScript rules.
     */
    function toInt32()
    {
        $v = $this->toInteger();
        if (is_infinite($v->value)) {
            return Runtime::$zero;
        }
        return Runtime::js_int((int)$v->value);
    }

    /**
     * @return Base JavaScript value converted to an integer using JavaScript rules.
     */
    function toInteger()
    {
        $v = $this->toNumber();
        if (is_nan($v->value)) {
            return Runtime::$zero;
        }
        if ($v->value == 0 or is_infinite($v->value)) {
            return $v;
        }
        return Runtime::js_int($v->value / abs($v->value) * floor(abs($v->value)));
    }

    /**
     * @return Base JavaScript value converted to a number using JavaScript rules.
     */
    function toNumber()
    {
        switch ($this->type) {
            case Base::UNDEFINED:
                return Runtime::$nan;
            case Base::NULL:
                return Runtime::$zero;
            case Base::BOOLEAN:
                return $this->value ? Runtime::$one : Runtime::$zero;
            case Base::NUMBER:
                return $this;
            case Base::STRING:
                return is_numeric($this->value) ? Runtime::js_int((float)$this->value) : Runtime::$nan;
            case Base::OBJECT:
                return $this->toPrimitive(Base::NUMBER)->toNumber();
        }
    }

    /**
     * @param null|int|string $hint Base::TYPE constant defining the value type.
     *
     * @return Base JavaScript value converted to a primitive (non-object/array) using JavaScript rules.
     */
    function toPrimitive($hint = null)
    {
        if ($this->type != Base::OBJECT) {
            return $this;
        }
        if ($hint != null) {
            $v = $this->defaultValue($hint);
        } else {
            $v = $this->defaultValue();
        }
        return $v;
    }

    /**
     * @return Base JavaScript value converted to an unsigned 32-bit integer using JavaScript rules.
     */
    function toUInt32()
    {
        $v = $this->toInteger();
        if (is_infinite($v->value)) {
            return Runtime::$zero;
        }
        return Runtime::js_int(bcmod($v->value, 4294967296)); // should keep a float.
    }

    /**
     * @return Base JavaScript value converted to an unsigned 16-bit integer using JavaScript rules.
     */
    function toUInt16()
    {
        $v = $this->toInteger();
        if (is_infinite($v->value)) {
            return Runtime::$zero;
        }
        return Runtime::js_int($v->value % 0x10000);
    }

    /**
     * @return Base JavaScript value converted to a string using JavaScript rules.
     */
    function toStr()
    {
        switch ($this->type) {
            case Base::UNDEFINED:
                return Runtime::js_str("undefined");
            case Base::NULL:
                return Runtime::js_str("null");
            case Base::BOOLEAN:
                return Runtime::js_str($this->value ? "true" : "false");
            case Base::STRING:
                return $this;
            case Base::OBJECT:
                return $this->toPrimitive(Base::STRING)->toStr();
            case Base::NUMBER:
                if (is_nan($this->value)) {
                    return Runtime::js_str("NaN");
                }
                if ($this->value == 0) {
                    return Runtime::js_str("0");
                }
                if ($this->value < 0) {
                    $v = Runtime::js_int(-$this->value)->toStr();
                    return Runtime::js_str("-" . $v->value);
                }
                if (is_infinite($this->value)) {
                    return Runtime::js_str("Infinity");
                }
                return Runtime::js_str((string)$this->value);
        }
    }

    /**
     * @return Base JavaScript value converted to an object using JavaScript rules.
     *
     * @throws jsException
     */
    function toObject()
    {
        switch ($this->type) {
            case Base::UNDEFINED:
            case Base::NULL:
                throw new jsException(new jsTypeError("Cannot convert null or undefined to objects"));
                /* XXX Throw a TypeError exception */
                return null;
            case Base::BOOLEAN:
                return new jsBoolean($this);
            case Base::NUMBER:
                return new jsNumber($this);
            case Base::STRING:
                return new jsString($this);
            case Base::OBJECT:
                return $this;
        }
        throw new jsException(new jsTypeError("Do not know how to convert value of type '{$this->type}'."));
    }

    /**
     * @return Base Simple debug output.
     */
    function toDebug()
    {
        switch ($this->type) {
            case Base::UNDEFINED:
                return "undefined";
            case Base::NULL:
                return "null";
            case Base::BOOLEAN:
                return $this->value ? "true" : "false";
            case Base::NUMBER:
                return $this->value;
            case Base::STRING:
                return var_export($this->value, 1);
            case Base::OBJECT:
                $s = "class: " . array_pop(explode('\\', get_class($this))) . "<br>";
                foreach ($this->slots as $key => $value) {
                    $s .= "$key => " . $value->value . "<br>";
                }
                return $s;
        }
    }

    /**
     * @return Base JavaScript value converted to a 32-bit integer using JavaScript rules.
     */
    function getValue()
    {
        //TODO: Evaluate why this is here, remove if useless. Must be a better way to handle this?
        // this should never get called, unless we have a logic bug.
        echo "##useless getValue##";
        flush();
        echo "<pre>";
        debug_print_backtrace();
        echo "</pre>";
        return $this;
    }

    function putValue($w)
    {
        //TODO: Evaluate why this is here, remove if useless. Must be a better way to handle this?
        throw new jsException(new jsReferenceError(VarDumper::dumpAsString($w)));
    }
}

