<?php

namespace js4php5\compiler\constructs;

use js4php5\compiler\Compiler;

class c_try extends BaseConstruct
{
    public $body;
    public $catch;
    public $final;

    /** @var string */
    public $id_try;

    /** @var string */
    public $id_catch;

    /** @var string */
    public $id_finally;

    /**
     * @param c_block $code
     * @param c_catch|null $catch
     * @param c_block|null $final
     */
    function __construct($code, $catch = null, $final = null)
    {
        $this->body = $code;
        $this->catch = $catch;
        $this->final = $final;
        $this->id_try = Compiler::generateSymbol("jsrt_try");
        $this->id_catch = Compiler::generateSymbol("jsrt_catch");
        $this->id_finally = Compiler::generateSymbol("jsrt_finally");
    }

    /**
     * @return string
     */
    function toplevel_emit()
    {
        $o = "function " . $this->id_try . "() {\n";
        $o .= "  try ";
        $o .= trim(str_replace("\n", "\n  ", $this->body));
        $o .= " catch (Exception \$e) {\n";
        $o .= "    Runtime::\$exception = \$e;\n";
        $o .= "  }\n";
        $o .= "  return NULL;\n";
        $o .= "}\n";
        if ($this->catch != null) {
            $o .= "function " . $this->id_catch . "() {\n";
            $o .= "  " . trim(str_replace("\n", "\n  ", $this->catch));
            $o .= "\n  return NULL;\n";
            $o .= "}\n";
        }
        if ($this->final != null) {
            $o .= "function " . $this->id_finally . "() {\n";
            $o .= "  " . trim(str_replace("\n", "\n  ", $this->final));
            $o .= "\n  return NULL;\n";
            $o .= "}\n";
        }
        return $o;
    }

    /**
     * @param bool $unusedParameter
     *
     * @return string
     */
    function emit($unusedParameter = false)
    {
        // so we put catch() and finally blocks in functions to be able to pick if/when to evaluate them
        // it's not clear why try is in a function too at this Point. consistency? yeah, weak.
        c_source::addFunctionDefinition($this);
        $id = ($this->catch != null) ? $this->catch->id : '';
        $this->body = $this->body->emit(true);
        if ($this->catch != null) {
            $this->catch = $this->catch->emit(true);
        }
        if ($this->final != null) {
            $this->final = $this->final->emit(true);
        }
        $ret = Compiler::generateSymbol("jsrt_ret");
        $tmp = Compiler::generateSymbol("jsrt_tmp");

        // try is on its own to work around a crash in my version of php5
        // apparently, php exceptions inside func_user_call()ed code are not all that stable just yet.
        // XXX note: the crash can still occur. still not entirely sure how it happens.
        // it feels like exceptions thrown from call_user_func-ed code corrupt some php internals, which
        // result in a possible crash at a later Point in the program flow.
        $o = "\$$tmp = " . $this->id_try . "();\n";
        $o .= "\$$ret = Runtime::trycatch(\$$tmp, ";
        $o .= ($this->catch != null ? "'" . $this->id_catch . "'" : "NULL") . ", ";
        $o .= ($this->final != null ? "'" . $this->id_finally . "'" : "NULL");
        $o .= ($this->catch != null ? ", '" . $id . "'" : "") . ");\n";
        $o .= "if (\$$ret != NULL) return \$$ret;\n";
        return $o;
    }
}

