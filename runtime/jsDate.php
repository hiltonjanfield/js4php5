<?php

namespace js4php5\runtime;

class jsDate extends jsObject
{
    function __construct($y = null, $m = null, $d = null, $h = null, $mn = null, $s = null, $ms = null)
    {
        parent::__construct("Date", Runtime::$proto_date);
        $y = ($y == null) ? Runtime::$undefined : $y;
        $m = ($m == null) ? Runtime::$undefined : $m;
        $d = ($d == null) ? Runtime::$undefined : $d;
        $h = ($h == null) ? Runtime::$undefined : $h;
        $mn = ($mn == null) ? Runtime::$undefined : $mn;
        $s = ($s == null) ? Runtime::$undefined : $s;
        $ms = ($ms == null) ? Runtime::$undefined : $ms;
        if ($y == Runtime::$undefined) {
            $value = floor(microtime(true) * 1000);
        } elseif ($m == Runtime::$undefined) {
            $v = $y->toPrimitive();
            if ($v->type == Base::STRING) {
                $value = strtotime($v->value) * 1000;
            } else {
                $value = $v->toNumber()->value;
            }
        } else {
            $y = $y->toNumber()->value;
            $m = $m->toNumber()->value;
            $d = ($d == Runtime::$undefined) ? 1 : $d->toNumber()->value;
            $h = ($h == Runtime::$undefined) ? 0 : $h->toNumber()->value;
            $mn = ($mn == Runtime::$undefined) ? 0 : $mn->toNumber()->value;
            $s = ($s == Runtime::$undefined) ? 0 : $s->toNumber()->value;
            $ms = ($ms == Runtime::$undefined) ? 0 : $ms->toNumber()->value;
            if (!is_nan($y)) {
                $y2k = floor($y);
                if ($y2k >= 0 and $y2k <= 99) {
                    $y = 1900 + $y2k;
                }
            }
            $value = 1000 * mktime($h, $mn, $s, $m + 1, $d, $y) + $ms;
        }
        $this->value = $value;
    }
    ////////////////////////
    // scriptable methods //
    ////////////////////////
    static function object($value)
    {
        list($y, $m, $d, $h, $m, $s, $ms) = func_get_args();
        if (jsFunction::isConstructor()) {
            return new jsDate($y, $m, $d, $h, $m, $s, $ms);
        } else {
            $d = new jsDate($y, $m, $d, $h, $m, $s, $ms);
            return $d->toStr();
        }
    }

    static function parse($v)
    {
        return Runtime::js_int(strtotime($v->toStr()->value) * 1000);
    }

    static function UTC($y, $m, $d, $h, $mn, $s, $ms)
    {
        $y = $y->toNumber()->value;
        $m = $m->toNumber()->value;
        $d = ($d == Runtime::$undefined) ? 1 : $d->toNumber()->value;
        $h = ($h == Runtime::$undefined) ? 0 : $h->toNumber()->value;
        $mn = ($mn == Runtime::$undefined) ? 0 : $mn->toNumber()->value;
        $s = ($s == Runtime::$undefined) ? 0 : $s->toNumber()->value;
        $ms = ($ms == Runtime::$undefined) ? 0 : $ms->toNumber()->value;
        if (!is_nan($y)) {
            $y2k = floor($y);
            if ($y2k >= 0 and $y2k <= 99) {
                $y = 1900 + $y2k;
            }
        }
        $value = 1000 * gmmktime($h, $mn, $s, $m + 1, $d, $y) + $ms;
        return Runtime::js_int($value);
    }

    static function toString()
    {
        // Gecko: Sat Jun 25 2005 02:55:46 GMT -0700 (Pacific Daylight Time)
        // MSIE: Sat Jun 25 02:56:25 PDT 2005
        // let's go with RFC 2822
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_str(date("r", $obj->value / 1000));
    }

    static function toDateString()
    {
        // Gecko: Sat Jun 25 2005
        // MSIE: Sat Jun 25 2005
        // they agree. weird.
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_str(date("D M j Y", $obj->value / 1000));
    }

    static function toTimeString()
    {
        // Gecko: 03:13:37 GMT -0700 (Pacific Daylight Time)
        // MSIE: 03:14:00 PDT
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_str(date("G:i:s T", $obj->value / 1000));
    }

    static function toLocaleString()
    {
        // Gecko: Saturday, June 25, 2005 03:15:55
        // MSIE: Saturday, June 25, 2005 03:16:21 AM
        // Us: Whatever PHP wants to do.
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_str(strftime("%c", $obj->value / 1000));
    }

    static function toLocaleDateString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_str(strftime("%x", $obj->value / 1000));
    }

    static function toLocaleTimeString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_str(strftime("%X", $obj->value / 1000));
    }

    static function getTime()
    {
        return jsDate::valueOf();
    }

    static function valueOf()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        return Runtime::js_int($obj->value);
    }

    static function getFullYear()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("Y", $t / 1000));
    }

    static function getUTCFullYear()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("Y", $t / 1000));
    }

    static function getDay()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("w", $t / 1000));
    }

    static function getUTCDay()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("w", $t / 1000));
    }

    static function getMillieconds()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int($t % 1000);
    }

    static function getUTCMilliseconds()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int($t % 1000);
    }

    static function getTimezoneOffset()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        $s = gettimeofday();
        return Runtime::js_int($t["minuteswest"]);
    }

    static function setTime($time)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $v = $time->toNumber()->value;
        $obj->value = $v;
        return Runtime::js_int($v);
    }

    static function setUTCMilliseconds($ms)
    {
        return jsDate::setMilliseconds($ms);
    }

    static function setMilliseconds($ms)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = jsDate::valueOf()->value;
        $ms = $ms->toNumber()->value;
        $v = floor($t / 1000) * 1000 + $ms;
        $obj->value = $v;
        return $v;
    }

    static function setUTCSeconds($s, $ms)
    {
        return jsDate::setSeconds($s, $ms);
    }

    static function setSeconds($s, $ms)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $s = $s->toNumber()->value;
        $ms = ($ms == Runtime::$undefined) ? ($t % 1000) : $ms->toNumber()->value;
        $v = floor($t / 60000) * 60000 + (1000 * $s + $ms);
        $obj->value = $v;
        return $v;
    }

    static function setMinutes($min, $sec, $ms)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $min = $min->toNumber()->value;
        $sec = ($sec == Runtime::$undefined) ? jsDate::getSeconds() : $sec->toNumber()->value;
        $ms = ($ms == Runtime::$undefined) ? ($t % 1000) : $ms->toNumber()->value;
        $v = mktime(jsDate::getHours(), $min, $sec, jsDate::getMonth(),
                jsDate::getDate(), jsDate::getYear()) * 1000 + $ms;
        $obj->value = $v;
        return $v;
    }

    static function getSeconds()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("s", $t / 1000));
    }

    static function getHours()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("G", $t / 1000));
    }

    static function getMonth()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("n", $t / 1000) - 1);
    }

    static function getDate()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("j", $t / 1000));
    }

    static function setUTCMinutes($min, $sec, $ms)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $min = $min->toNumber()->value;
        $sec = ($sec == Runtime::$undefined) ? jsDate::getUTCSeconds() : $sec->toNumber()->value;
        $ms = ($ms == Runtime::$undefined) ? ($t % 1000) : $ms->toNumber()->value;
        $v = gmmktime(jsDate::getUTCHours(), $min, $sec, jsDate::getUTCMonth(),
                jsDate::getUTCDate(), jsDate::getUTCYear()) * 1000 + $ms;
        $obj->value = $v;
        return $v;
    }

    static function getUTCSeconds()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("s", $t / 1000));
    }

    static function getUTCHours()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("G", $t / 1000));
    }

    static function getUTCMonth()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("n", $t / 1000) - 1);
    }

    static function getUTCDate()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("j", $t / 1000));
    }

    static function setHours($hour, $min, $sec, $ms)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $hour = $hour->toNumber()->value;
        $min = ($min == Runtime::$undefined) ? jsDate::getMinutes() : $min->toNumber()->value;
        $sec = ($sec == Runtime::$undefined) ? jsDate::getSeconds() : $sec->toNumber()->value;
        $ms = ($ms == Runtime::$undefined) ? ($t % 1000) : $ms->toNumber()->value;
        $v = mktime($hour, $min, $sec, jsDate::getMonth(),
                jsDate::getDate(), jsDate::getYear()) * 1000 + $ms;
        $obj->value = $v;
        return $v;
    }

    static function getMinutes()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(date("i", $t / 1000));
    }

    static function setUTCHours($hour, $min, $sec, $ms)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $hour = $hour->toNumber()->value;
        $min = ($min == Runtime::$undefined) ? jsDate::getUTCMinutes() : $min->toNumber()->value;
        $sec = ($sec == Runtime::$undefined) ? jsDate::getUTCSeconds() : $sec->toNumber()->value;
        $ms = ($ms == Runtime::$undefined) ? ($t % 1000) : $ms->toNumber()->value;
        $v = gmmktime($hour, $min, $sec, jsDate::getUTCMonth(),
                jsDate::getUTCDate(), jsDate::getUTCYear()) * 1000 + $ms;
        $obj->value = $v;
        return $v;
    }

    static function getUTCMinutes()
    {
        $t = jsDate::valueOf()->value;
        if (is_nan($t)) {
            return Runtime::$nan;
        }
        return Runtime::js_int(gmdate("i", $t / 1000));
    }

    static function setDate($date)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $date = $date->toNumber()->value;
        $v = mktime(jsDate::getHours(), jsDate::getMinutes(), jsDate::getSeconds(),
                jsDate::getMonth(), $date, jsDate::getYear()) * 1000 + ($t % 1000);
        $obj->value = $v;
        return $v;
    }

    static function setUTCDate($date)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $date = $date->toNumber()->value;
        $v = gmmktime(jsDate::getUTCHours(), jsDate::getUTCMinutes(), jsDate::getUTCSeconds(),
                jsDate::getUTCMonth(), $date, jsDate::getUTCYear()) * 1000 + ($t % 1000);
        $obj->value = $v;
        return $v;
    }

    static function setMonth($month, $date)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $month = $month->toNumber()->value;
        $date = ($date == Runtime::$undefined) ? jsDate::getDate() : $date->toNumber()->value;
        $v = mktime(jsDate::getHours(), jsDate::getMinutes(), jsDate::getSeconds(),
                $month, $date, jsDate::getYear()) * 1000 + ($t % 1000);
        $obj->value = $v;
        return $v;
    }

    static function setUTCMonth($month, $date)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $month = $month->toNumber()->value;
        $date = ($date == Runtime::$undefined) ? jsDate::getUTCDate() : $date->toNumber()->value;
        $v = gmmktime(jsDate::getUTCHours(), jsDate::getUTCMinutes(), jsDate::getUTCSeconds(),
                $month, $date, jsDate::getUTCYear()) * 1000 + ($t % 1000);
        $obj->value = $v;
        return $v;
    }

    static function setFullYear($year, $month, $date)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $year = $year->toNumber()->value;
        $month = ($month == Runtime::$undefined) ? jsDate::getMonth() : $month->toNumber()->value;
        $date = ($date == Runtime::$undefined) ? jsDate::getMinutes() : $date->toNumber()->value;
        $v = mktime(jsDate::getHours(), jsDate::getDate(), jsDate::getSeconds(),
                $month, $date, $year) * 1000 + ($t % 1000);
        $obj->value = $v;
        return $v;
    }

    static function setUTCFullYear($year, $month, $date)
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        $year = $year->toNumber()->value;
        $month = ($month == Runtime::$undefined) ? jsDate::getUTCMonth() : $month->toNumber()->value;
        $date = ($date == Runtime::$undefined) ? jsDate::getUTCDate() : $date->toNumber()->value;
        $v = gmmktime(jsDate::getUTCHours(), jsDate::getUTCMinutes(), jsDate::getUTCSeconds(),
                $month, $date, $year) * 1000 + ($t % 1000);
        $obj->value = $v;
        return $v;
    }

    static function toUTCString()
    {
        $obj = Runtime::this();
        if (!($obj instanceof jsDate)) {
            throw new jsException(new jsTypeError());
        }
        $t = $obj->value;
        return Runtime::js_str(gmstrftime("%c", $t / 1000));
    }

}

?>