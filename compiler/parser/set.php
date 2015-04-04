<?php

namespace js4php5\compiler\parser;

/*
File: set.so.php
License: GPL
Purpose: We should really have a "set" data type. It's too useful.
*/

class set
{
    function set($list = array())
    {
        $this->data = array_count_values($list);
    }

    function has($item)
    {
        return isset($this->data[$item]);
    }

    function add($item)
    {
        $this->data[$item] = true;
    }

    function del($item)
    {
        unset($this->data[$item]);
    }

    function all()
    {
        return array_keys($this->data);
    }

    function one()
    {
        return key($this->data);
    }

    function count()
    {
        return count($this->data);
    }
}


