<?php

namespace js4php5\runtime;

class jsAttribute
{

    public $value;

    public $readonly = false;

    public $dontenum = false;

    public $dontdelete = false;

    function __construct($value, $ro = 0, $de = 0, $dd = 0)
    {
        $this->value = $value;
        $this->readonly = $ro;
        $this->dontenum = $de;
        $this->dontdelete = $dd;
    }
}

