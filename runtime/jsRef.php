<?php

namespace js4php5\runtime;

class jsRef
{

    public $type;

    public $base;

    public $propName;

    function __construct($base, $propName)
    {
        $this->type = Base::REF;
        $this->base = $base;
        $this->propName = $propName;
    }

    function getValue()
    {
        if (!is_object($this->base)) {
            echo "<pre>";
            debug_print_backtrace();
            echo "</pre>";
        }
        return $this->base->get($this->propName);
    }

    function putValue($w, $ret = 0)
    {
        $v = null;
        if ($ret == 2) {
            $v = $this->base->get($this->propName);
        }
        $this->base->put($this->propName, $w);
        if ($ret == 1) {
            return $w;
        }
        return $v;
    }
}

?>