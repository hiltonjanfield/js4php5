<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\JS;
use hiltonjanfield\js4php5\compiler\Compiler;

class c_for_in extends BaseConstruct
{
    private $one;
    private $list;
    private $statement;

    function __construct($one, $list, $statement)
    {
        $this->one = $one;
        $this->list = $list;
        $this->statement = $statement;
    }

    function emit($unusedParameter = false)
    {
        $key = Compiler::generateSymbol("fv");
        c_source::$nest++;
        $o = "foreach (" . $this->list->emit(true) . " as \$$key) {\n";
//        if (JS::getClassNameWithoutNamespace($this->one) == "c_var") {
        if ($this->one instanceof c_var) {
            $v = $this->one->emit_for();
        } else {
            $v = $this->one->emit();
        }
        $o .= "  Runtime::expr_assign($v, Runtime::js_str(\$$key));\n";
        $o .= "  " . trim(str_replace("\n", "\n  ", $this->statement->emit(true))) . "\n";
        $o .= "}";
        c_source::$nest--;
        return $o;
    }
}

