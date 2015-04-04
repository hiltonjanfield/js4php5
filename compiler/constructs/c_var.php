<?php

namespace js4php5\compiler\constructs;

use hiltonjanfield\js4php5\VarDumper;

class c_var extends BaseConstruct
{

    /** @var array */
    public $vars;

    /**
     * @param array $args Array where each value contains [[0] => 'var_name', [1] => constructs\BaseConstruct object]
     */
    function __construct(array $args)
    {
        //TODO: Find out why 'var foobar;' (declare but don't initialize) gets value null instead of c_literal_null.
        // Maybe need to add a js_undefined object and update parse rules?
        //TODO: After fixed (if needs to be), remove hack in BaseBinaryConstruct::emit().
        $this->vars = $args;
    }

    static public function really_emit($arr)
    {
        if (count($arr) == 0) {
            return '';
        }
        $l = "'" . implode("','", array_unique($arr)) . "'";
        return "Runtime::define_variables($l);\n";
    }

    function emit_for()
    {
        $this->emit(true);
        return "Runtime::id('" . $this->vars[0][0] . "')";
    }

    /**
     * @param bool $unusedParameter
     *
     * @return string PHP code chunk
     */
    function emit($unusedParameter = false)
    {
        $o = '';
        foreach ($this->vars as $var) {
            /**
             * @var string        $id
             * @var BaseConstruct $init
             */
            list($id, $init) = $var;
            c_source::$that->addVariable($id);
            if (get_class($init)) {
                $obj = new c_assign(new c_identifier($id), $init);
                $o .= $obj->emit(true);
                $o .= ";\n";
            }
        }
        return $o;
    }
}

