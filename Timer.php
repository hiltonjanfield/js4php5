<?php

namespace js4php5;


/**
 * Simple execution timer class to aid in js4php5 development.
 *
 * To be removed at a later date. Or moved to it's own project with enhancements.
 *
 * Ideas:
 * - Implement an 'add' function where chunks of code can be incrementally timed, like stopping and starting a stopwatch.
 */
class Timer {

    /**
     * @var (int|float)[]
     */
    private static $timers = [];

    /**
     * @var int
     */
    private static $counter = 0;

    /**
     * Start a timer. Erases the previous time if a previously-timed label is used.
     *
     * @param null|string $label Timer ID. If none given, an incremental numeric ID will be used.
     */
    public static function start($label = null) {
        if ($label === null) {
            $label = static::$counter++;
        }

        static::$timers[$label] = microtime(true);
    }

    /**
     * Stop a timer. Return the time taken in seconds (float).
     *
     * @param null|string $label Timer ID. If none given, the last used numeric ID will be used.
     *
     * @return float
     */
    public static function stop($label = null) {
        if ($label === null) {
            $label = static::$counter;
        }

        if (isset(static::$timers[$label])) {
            static::$timers[$label] = microtime(true) - static::$timers[$label];

            return static::$timers[$label];
        }

        return 0;
    }

    /**
     * Get current or saved execution time.
     *
     * If a timer has been stopped, it will return the recorded time.
     * If a timer has NOT been stopped, it will return the execution time so far, but will not stop the timer.
     *
     * @param null|string $label Timer ID. If none given, the last used numeric ID will be used.
     *
     * @return float
     */
    public static function get($label = null) {
        if ($label === null) {
            $label = static::$counter;
        }

        if (!isset(static::$timers[$label])) {
            return 0;
        }
        $time = static::$timers[$label];
        if ($time > 1000) {
            return microtime(true) - static::$timers[$label];
        }
        return static::$timers[$label];
    }

    /**
     * Output a simple list of all stored times and their labels.
     *
     * @param bool $ms If true, output in milliseconds (51 ms); if false, output in seconds (0.0519234756 s).
     */
    public static function output($ms = false) {
        echo '<hr><h2>Times:</h2><pre>';

        $maxkeylen = 0;
        foreach (static::$timers as $key => $time) {
            $keylen = strlen($key);
            if ($keylen > $maxkeylen) {
                $maxkeylen = $keylen;
            }
        }

        foreach (static::$timers as $key => $time) {
            echo str_pad($key, $maxkeylen, '.') . ': ';
            echo $ms ? round(static::get($key) * 1000) . " ms\n" : static::get($key) . " s\n";
        }

        echo '</pre><hr>';
    }

    /**
     *
     */
    public static function output_ms() {
        static::output(true);
    }

}
