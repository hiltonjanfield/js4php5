<?php

namespace js4php5\runtime;

use Exception;
use js4php5\JS;

class jsFunction extends jsObject
{

    static $constructor;

    protected $name;

    protected $phpname;

    protected $args;

    protected $scope = array();

    function __construct($name = '', $phpname = 'jsi_empty', $args = array(), $scope = null)
    {
        parent::__construct("Function", Runtime::$proto_function);
        if ($scope == null) {
            $scope = Runtime::$contexts[0]->scope_chain;
        }
        $this->name = $name;
        $this->phpname = $phpname;
        $this->args = $args;
        $this->scope = $scope;
        $this->put("length", new Base(Base::NUMBER, count($args)), array("dontdelete", "readonly", "dontenum"));
        $obj = new jsObject("Object");
        $obj->put("constructor", $this, array("dontenum"));
        $this->put("prototype", $obj, array("dontdelete"));
    }

    static function isConstructor()
    {
        return self::$constructor;
    }

    static public function func_object($value)
    {
        throw new jsException(new jsSyntaxError("new Function(..) not implemented"));
    }

    /* When the [[Call]] property for a Function object F is called, the following steps are taken:
    1. Establish a new execution context using F's FormalParameterList, the passed arguments list, and the this value as described in 10.2.3.
    2. Evaluate F's FunctionBody.
    3. Exit the execution context established in step 1, restoring the previous execution context.
    */

    static public function func_toString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsFunction)) {
            throw new jsException(new jsTypeError());
        }
        return $obj->defaultValue();
    }

    static public function func_apply($thisArg, $argArray)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsFunction)) {
            throw new jsException(new jsTypeError());
        }
        if ($thisArg == Runtime::$null or $thisArg == Runtime::$undefined) {
            $thisArg = Runtime::$global;
        } else {
            $thisArg = $thisArg->toObject();
        }
        if ($argArray = Runtime::$null or $argArray == Runtime::$undefined) {
            $argArray = array();
        } else {
            // check for a length property
            if ($argArray->hasProperty("length")) {
                $argArray = jsArray::toNativeArray($argArray);
            } else {
                throw new jsException(new jsTypeError("second argument to apply() must be an array"));
            }
        }
        return $obj->_call($thisArg, $argArray);
    }

    static public function func_call($thisArg)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsFunction)) {
            throw new jsException(new jsTypeError());
        }
        $args = func_get_args();
        array_shift($args);
        if ($thisArg == Runtime::$null or $thisArg == Runtime::$undefined) {
            $thisArg = Runtime::$global;
        } else {
            $thisArg = $thisArg->toObject();
        }
        return $obj->_call($thisArg, $args);
    }

    ////////////////////////
    // scriptable methods //
    ////////////////////////

    function construct($args)
    {
        $obj = new jsObject("Object");
        $proto = $this->get("prototype");
        if ($proto->type == Base::OBJECT) {
            $obj->prototype = $proto;
        } else {
            $obj->prototype = Runtime::$proto_object;
        }
        #-- [[Call]]
        $v = $this->_call($obj, $args, 1);
        if ($v and $v->type == Base::OBJECT) {
            return $v;

        }
        return $obj;
    }

    function _call($that, $args = array(), $constructor = 0)
    {
        jsFunction::$constructor = $constructor;
        #-- new activation object
        $var = new jsObject("Activation");
        #-- populate stuff
        $arguments = new jsObject();
        $var->put("arguments", $arguments);
        $len = count($args);
        for ($i = 0; $i < count($this->args); $i++) {
            if (!isset($args[$i])) {
                $args[$i] = Runtime::$undefined;
            } else {
                if ($args[$i] instanceof jsRef) {
                    echo "<pre>";
                    echo "jsRef as $i-th argument of call\n";
                    debug_print_backtrace();
                    echo "</pre>";
                }
                //$args[$i] = $args[$i]->getValue(); // we don't pass by reference
            }
            $var->put($this->args[$i], $args[$i]);
            #-- enforce the weird "changing one changes the other" rule
            $arguments->slots[$this->args[$i]] = $var->slots[$this->args[$i]];
            $arguments->slots[$i] = $var->slots[$this->args[$i]];
        }
        if ($len > count($this->args)) {
            #-- unnammed extra arguments
            for ($i = count($this->args); $i < $len; $i++) {
                $arguments->put($i, $args[$i]);
            }
        }
        $arguments->put("callee", $this, array("dontenum"));
        $arguments->put("length", new Base(Base::NUMBER, $len), array("dontenum"));
        $scope = $this->scope;
        array_unshift($scope, $var);
        #-- new context
        $context = new jsContext($that, $scope, $var);
        array_unshift(Runtime::$contexts, $context);
        $thrown = null;
        // echo "function name=".serialize($this->phpname)." arguments = ".serialize($args)."<hr>";
        try {
            // gross hack to hide warnings triggered by exception throwing.
            // this way, we still get to see other kind of errors. unless they're warnings. sigh.
            // note: this call_user_func_array() is responsible for crashes if exceptions are thrown through it.
            //$saved = error_reporting(4093);
            if (!is_array($this->phpname)) {
                $this->phpname = array(
                    JS::getCurrentScriptFQCN(),
                    $this->phpname
                );
            }
                if ($this->phpname[0] == 'Runtime') {
                $this->phpname[0] = 'js4php5\runtime\Runtime';
            }
            if ($this->phpname[0] == 'jsObject') {
                $this->phpname[0] = 'js4php5\runtime\jsObject';
            }
            if ($this->phpname[0] == 'jsMath') {
                $this->phpname[0] = 'js4php5\runtime\jsMath';
            }
            if ($this->phpname[0] == 'jsString') {
                $this->phpname[0] = 'js4php5\runtime\jsString';
            }

            $v = call_user_func_array($this->phpname, $args);
            //error_reporting($saved);
        } catch (Exception $e) {
            $thrown = $e;
            //error_reporting($saved);
        }
        array_shift(Runtime::$contexts);
        // we restored context, time to follow-through on those exceptions.
        if ($thrown != null) {
            throw $thrown;
        }
        //TODO: PHP5ize: Change the above lines to a try..catch..finally block.
        return $v ? $v : Runtime::$undefined;
    }

    function defaultValue($iggy = null)
    {
        $o = "function " . $this->name . "(";
        $o .= implode(",", $this->args);
        $o .= ") {\n";
        $o .= " [ function body ] \n";
        $o .= "}\n";
        return Runtime::js_str($o);
    }

    function hasInstance($value)
    {
        if ($value->type != Base::OBJECT) {
            return Runtime::$false;
        }
        $obj = $this->get("prototype");
        if ($obj->type != Base::OBJECT) {
            throw new jsException(new jsTypeError('XXX'));
        }
        do {
            $value = $value->prototype;
            if ($value == null) {
                return Runtime::$false;
            }
            if ($obj == $value) {
                return Runtime::$true;
            }
        } while (true);
    }

}

