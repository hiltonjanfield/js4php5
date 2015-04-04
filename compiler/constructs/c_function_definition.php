<?php

namespace js4php5\compiler\constructs;

use js4php5\compiler\Compiler;

class c_function_definition extends BaseConstruct
{

    static $in_function = 0;

    function __construct($a)
    {
        list($this->id, $this->params, $this->body) = $a;
        $this->phpid = Compiler::generateSymbol("jsrt_uf");
    }

    function toplevel_emit()
    {
        $o = "    static public function " . $this->phpid . "() {\n";
        $o .= "        " . trim(str_replace("\n", "\n        ", $this->body));
        $o .= "\n    }\n\n";
        return $o;
    }

    function function_emit()
    {
        self::$in_function++;
        $this->body = $this->body->emit(true); // do it early, to catch inner functions
        self::$in_function--;
        c_source::addFunctionDefinition($this);
        $id = "";
        if (true or $this->id != '') {
            $id = ",'" . $this->id . "'";
        }
        $p = "";
        if (count($this->params) > 0) {
            $p = ",array('" . implode("','", $this->params) . "')";
        }
        return "Runtime::define_function('" . $this->phpid . "'" . $id . $p . ");\n";
    }

    function emit($unusedParameter = false)
    {
        #-- if this gets called, we're a function inside an expression.
        c_source::addFunctionExpression($this);
        #-- XXX output something that will return a handle to the function.
        return "Runtime::function_id('" . $this->phpid . "')";
    }
}
