<?php

namespace js4php5\runtime;

use Exception;

class jsException extends Exception
{

    const EXCEPTION = 7;

    const NORMAL = 8;

    public $type;

    public $value;

    function __construct($value)
    {
        parent::__construct();
        $this->type = self::EXCEPTION;
        $this->value = $value;
    }
}

?>