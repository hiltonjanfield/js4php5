<?php

namespace js4php5\compiler\constructs;

class c_literal_string extends BaseConstruct
{

    function __construct($a, $stripquotes = 1)
    {
        if ($stripquotes) {
            $a = substr($a, 1, strlen($a) - 2);
        }
        $this->str = $this->parse_string($a);
    }

    function parse_string($str)
    {
        $out = '';
        $mode = 0;
        foreach (str_split($str) as $c) {
            switch ($mode) {
                case 0:
                    if ($c == '\\') {
                        $mode = 1;
                    } else {
                        $out .= $c;
                    }
                    break;
                case 1:
                    $mode = 0;
                    switch ($c) {
                        case "'":
                            $out .= "'";
                            break;
                        case '"':
                            $out .= '"';
                            break;
                        case '\\':
                            $out .= "\\";
                            break;
                        case 'b':
                            $out .= chr(8);
                            break;
                        case 'f':
                            $out .= chr(12);
                            break;
                        case 'n':
                            $out .= chr(10);
                            break;
                        case 'r':
                            $out .= chr(13);
                            break;
                        case 't':
                            $out .= chr(9);
                            break;
                        case 'v':
                            $out .= chr(11);
                            break;
                        case '0':
                            $out .= chr(0);
                            break; // not quite right. \040 fails.
                        case 'x':
                            $mode = 2;
                            $b = '';
                            break;
                        case 'u':
                            $mode = 4;
                            $b = '';
                            break;
                        default:
                            $out .= $c;
                            break;
                    }
                    break;
                case 2:
                    $b .= $c;
                    if (stripos("0123456789abcdef", $c) !== false) {
                        $mode = 3;
                    } else {
                        $out .= 'x' . $b;
                        $mode = 0;
                    }
                    break;
                case 3:
                    $b .= $c;
                    if (stripos("0123456789abcdef", $c) !== false) {
                        $out .= chr(hexdec($b));
                        $mode = 0;
                    } else {
                        $out .= 'x' . $b;
                        $mode = 0;
                    }
                    break;
                case 4:
                    $b .= $c;
                    if (stripos("0123456789abcdef", $c) !== false) {
                        $mode = 5;
                    } else {
                        $out .= 'u' . $b;
                        $mode = 0;
                    }
                    break;
                case 5:
                    $b .= $c;
                    if (stripos("0123456789abcdef", $c) !== false) {
                        $mode = 6;
                    } else {
                        $out .= 'u' . $b;
                        $mode = 0;
                    }
                    break;
                case 6:
                    $b .= $c;
                    if (stripos("0123456789abcdef", $c) !== false) {
                        $mode = 7;
                    } else {
                        $out .= 'u' . $b;
                        $mode = 0;
                    }
                    break;
                case 7:
                    $b .= $c;
                    if (stripos("0123456789abcdef", $c) !== false) {
                        $out .= chr(hexdec($b));
                        $mode = 0;
                    } else {
                        $out .= 'u' . $b;
                        $mode = 0;
                    }
                    break;
            }
        }
        return $out;
    }

    function emit($unusedParameter = false)
    {
        return "Runtime::js_str(" . var_export($this->str, 1) . ")";
    }
}

