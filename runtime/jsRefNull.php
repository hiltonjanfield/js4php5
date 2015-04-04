<?php

namespace js4php5\runtime;

use hiltonjanfield\js4php5\VarDumper;

class jsRefNull extends jsRef
{
    function __construct($propName)
    {
        parent::__construct(null, $propName);
    }

    function getValue()
    {
        echo "oops. trying to read " . $this->propName . ", but that's not defined.<hr>";
        throw new jsException(new jsReferenceError(VarDumper::dumpAsString($this)));
    }

    function putValue($w, $ret = 0)
    {
        Runtime::$global->put($this->propName, $w);
    }
}

?>