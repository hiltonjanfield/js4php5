<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\jsc\js_exception;
use hiltonjanfield\js4php5\jsc\js_syntaxerror;
use hiltonjanfield\js4php5\runtime\Base;

class c_return extends BaseConstruct
{
    function __construct($expr)
    {
        $this->expr = $expr;
    }

    function emit($unusedParameter = false)
    {
        // Removing this enables the called script to return values back to PHP.
        // Not normal JS behaviour, but useful for this setup.
//        if (c_function_definition::$in_function == 0) {
            // Script return (value is returned to PHP).
//            throw new jsException(new jsSyntaxError("invalid return"));
//            return "return " . $this->expr->emit(true) . ";\n";
//        }
        if ($this->expr == ';') {
            return 'return Runtime::$undefined;\n';
        } else {
            return "return " . $this->expr->emit(true) . ";\n";
        }
    }
}

