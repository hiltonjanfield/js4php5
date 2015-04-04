<?php

namespace js4php5\compiler\parser;

class state_set_labeler
{
    function state_set_labeler()
    {
        $this->map = array();
    }

    function label($list)
    {
        sort($list);
        $key = implode(':', $list);
        if (empty($this->map[$key])) {
            $this->map[$key] = Helpers::gen_label();
        }
        return $this->map[$key];
    }
}

