<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\runtime\jsFunction;

class c_source extends BaseConstruct
{

    /** @var c_source */
    static public $that;

    /** @var int */
    static public $nest;

    static public $labels;

    /** @var BaseConstruct[] */
    public $code;

    /** @var c_var[] */
    public $vars;

    /** @var jsFunction[] */
    public $functions;

    /** @var jsFunction[] */
    public $funcdef;

    /**
     * @param BaseConstruct[] $statements
     * @param jsFunction[]       $functions
     */
    function __construct($statements = array(), $functions = array())
    {
        $this->code = $statements;
        $this->functions = $functions;
        $this->vars = array();
        $this->funcdef = array(); // only used by toplevel object
    }

    /**
     * @param jsFunction $function
     */
    static public function addFunctionExpression($function)
    {
        c_source::$that->functions[] = $function;
    }

    /**
     * @param jsFunction $function
     */
    static public function addFunctionDefinition($function)
    {
        c_program::$source->funcdef[] = $function;
    }

    /**
     * @param BaseConstruct $statement
     */
    function addStatement($statement)
    {
        $this->code[] = $statement;
    }

    function addFunction($function)
    {
        $this->functions[] = $function;
    }

    function addVariable($var)
    {
        c_source::$that->vars[] = $var;
    }

    /**
     * @param bool $unusedParameter Ignored.
     *
     * @return string
     */
    function emit($unusedParameter = false)
    {
        self::$nest = 0;
        self::$labels = array();
        #dump the main body
        $saved_that = c_source::$that;
        c_source::$that = $this;
        $s = '';
        foreach ($this->code as $statement) {
            $s .= $statement->emit(true);
        }
        c_source::$that = $saved_that;
        #dump variable declarations now that we went through the body
        $v = c_var::really_emit($this->vars);
        #dump function expressions.
        $f = '';
        foreach ($this->functions as $function) {
            $f .= $function->function_emit();
        }
        if ($f != '') {
            $f = "/* function mapping */\n" . $f;
        }
        #if toplevel, dump function declarations
        $fd = "";
        if ($this === c_program::$source) {
            $fd = '';
            foreach ($this->funcdef as $function) {
                $fd .= $function->toplevel_emit();
            }
            if ($fd != '') {
                $fd = "/* function declarations */\n" . $fd;
            }
            # that's all folks
            return "    static public function run(){\n            JS::init();\n        " . $f . $v . $s . "\n}\n\n" . $fd;
        }
        # that's all folks
        return $fd . $f . $v . $s;
    }
}

