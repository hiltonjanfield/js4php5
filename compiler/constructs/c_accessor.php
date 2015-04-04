<?php

namespace js4php5\compiler\constructs;

/**
 * Construct for the Javascript 'dot' operator.
 */
class c_accessor extends BaseConstruct
{

    /** @var BaseConstruct */
    public $obj;

    /** @var BaseConstruct */
    public $member;

    /** @var bool */
    public $resolve;

    /**
     * @param BaseConstruct $obj
     * @param BaseConstruct $member
     * @param bool          $resolve
     */
    function __construct($obj, $member, $resolve)
    {
        $this->obj = $obj;
        $this->member = $member;
        $this->resolve = $resolve;
    }

    /**
     * @inheritdoc
     */
    function emit($getValue = false)
    {
        $v = $getValue ? "v" : "";
        return "Runtime::dot$v(" . $this->obj->emit(true) . "," . $this->member->emit($this->resolve) . ")";
    }
}
