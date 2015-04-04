<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

class c_catch extends BaseConstruct
{
    /** @var string */
    public $id;

    /** @var c_block */
    public $code;

    /**
     * @param string $id
     * @param c_block $code
     */
    function __construct($id, $code)
    {
        $this->id = $id;
        $this->code = $code;
    }

    function emit($unusedParameter = false)
    {
        // this kind of code makes you wonder why this is even an object. absorb me. please. XXX
        return $this->code->emit(true);
    }
}

