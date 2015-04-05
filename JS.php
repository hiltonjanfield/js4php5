<?php
//TODO: Error catching on all require/require_once() and eval() calls.
//TODO: Provide support to use an external caching system, e.g. the user's framework.

namespace js4php5;

use js4php5\compiler\Compiler;
use js4php5\runtime\jsAttribute;
use js4php5\runtime\Base;
use js4php5\runtime\Runtime;

/**
 * Class JS - Main API for JavaScript Compilation and Execution
 *
 * Basic Usage:
 *   JS::run('var js4php5 = "It works!"; print(js4php5);');
 *
 * Main methods:
 *   JS::run($script, [$id = null, [$forceRecompile = false]]);
 *     Compile, cache, and execute the script. If already cached, it will be used instead of compiling.
 *     Returns the value returned by the script, converted to a standard PHP value.
 *
 *   JS::getCurrentScriptId()
 *     Returns the ID of the last run script. Use this to get the ID of a script for which you did not
 *     provide your own ID.
 *
 *   JS::getCurrentScriptFQCN()
 *     Returns the Fully Qualified Class Name of the PHP version of the last run script.
 *     Once a script has been run, the class remains in memory until the end of the session.
 *     The following code will grab the script reference and run it again whenever you want without using JS:
 *       $myscript = JS::getCurrentScriptFQCN();  $myscript::run();
 *
 *   JS::convertReturnValue($value)
 *     If you are calling a script manually as above ($myscript::run()) and need the return value of the script,
 *     you can use this function to convert it to a usable PHP value.
 *
 *   JS::defineObject($objectName, [$functions = null, [$variables = null]])
 *     Define your own JavaScript object.
 *     $functions is an array consisting of 'javascriptFunctionName' => 'PHPFunctionName'.
 *       Named PHP functions must exist in the global namespace.
 *     $variables is an array consisting of 'javascriptVariableName' => value.
 *       Values must be standard values; no objects or arrays. If you require this functionality,
 *       use the Runtime:: API directly.
 *     Example:
 *       JS::defineObject('external', ['sha1' => 'js_sha1', 'add' => 'js_add'], ['PI' => 3.14159]);
 *     This creates a JavaScript object with:
 *     - external.sha1()
 *     - external.add()
 *     - external.PI
 *
 *   JS::setCacheDir($directory);
 *     Set the directory where cached files are kept.
 *     Returns FALSE if the directory is invalid or cannot be written to.
 *     If not called, this defaults to the temporary directory reported by PHP.
 *
 * See README.md for more information.
 */
class JS
{

    const VERSION = 0.1;

    /** String prefixed to script IDs to make a class name. */
    const CLASS_PREFIX = 'js4php5_';

    /** @var string Cache directory. */
    private static $cacheDir;

    /** @var string ID string of script being processed. */
    private static $currentScriptId;

    /** @var string Namespace of class for script being processed. */
    private static $currentScriptNamespace;

    /** @var string Name of class for script being processed. */
    private static $currentScriptClassName;

    /**
     * @return string
     */
    public static function getCurrentScriptNamespace()
    {
        return self::$currentScriptNamespace;
    }

    /**
     * Quick function to add a global object to the JavaScript interpreter.
     *
     * This object will be available to ALL scripts executed during this session.
     * Every function and variable is placed as a direct property of the created object.
     * Functions will have a length of 0 in JavaScript; this might affect some advanced scripts.
     * Functions will not have a prototype; this might affect some advanced scripts.
     *
     * Arrays and objects in variables is not yet supported.
     *
     * Example:
     *   JS::defineObject(
     *     'external', // Object name in JavaScript
     *     [
     *       'include' => 'my_js_include',  // defines function: external.include() which calls my_js_include() in PHP.
     *       'require' => 'my_js_require',  // defines function: external.require() which calls my_js_require() in PHP.
     *     ],
     *     [
     *       'PI' => 3.1415926535,          // defines variable external.PI
     *       'ZERO' => 0,                   // defines variable external.ZERO
     *     ]
     *   );
     *
     * A few notes:
     *  - this function create a new Object() and assign it as $objname on the global object.
     *  - every function and variable is placed as a direct property of $objname
     *  - those functions will have a length of 0. I'd be surprised if someone cares.
     *  - those functions won't have a prototype. Again, not likely to matter much.
     *  - variables cannot contain arrays or objects. use the real Runtime:: API for that stuff.
     *
     */
    public static function defineObject($objectName, $functions = null, $variables = null)
    {
        JS::init();

        // Define the main object.
        $obj = new Object();
        Runtime::define_variable($objectName, $obj);

        // Link functions.
        Runtime::push_context($obj);
        foreach ((array)$functions as $js => $php) {
            Runtime::define_function($php, $js);
        }
        Runtime::pop_context();

        // Set variables.
        foreach ((array)$variables as $js => $php) {
            $obj->put($js, static::convertVariable($php));
        }
    }

    /**
     * Convert PHP values to engine object-values.
     * Used in callFunction() and defineObject().
     *
     * @param $phpValue
     *
     * @return Base
     */
    private static function convertVariable($phpValue)
    {
        switch (true) {
            case is_bool($phpValue):
                return new Base(Base::BOOLEAN, $phpValue);
            case is_string($phpValue):
                return Runtime::js_str($phpValue);
            case is_numeric($phpValue):
                return Runtime::js_int($phpValue);
            case is_null($phpValue):
                return Runtime::$null;
            case is_array($phpValue):
                //TODO: Implement arrays/objects in JS::defineObject().
            default:
                return Runtime::$undefined;
        }
    }

    /**
     * Call a defined Javascript function by name.
     *
     * @param       $name
     * @param array $parameters
     *
     * @return mixed
     * @throws runtime\jsException
     */
    public static function callFunction($name, array $parameters)
    {
        $params = [];
        foreach ($parameters as $p) {
            $params[] = static::convertVariable($p);
        }
        $result = Runtime::call(Runtime::id($name), $params);

        return static::convertReturnValue($result);
    }

    /**
     * Init function used by generated code.
     *
     * Do not call this function directly.
     */
    public static function init()
    {
        Runtime::start_once();
    }

    /**
     * Allows the user to specify a cache directory. If not set, findCachedFile() will try to set one when used.
     *
     * @param $directory
     *
     * @return false|string
     */
    public static function setCacheDir($directory)
    {
        // Strip trailing slashes (if any).
        // We are comparing for '/' and '\' rather than DIRECTORY_SEPARATOR to catch both versions.
        $lastChar = substr(trim($directory), -1);
        if ($lastChar == '/' or $lastChar == '\\') {
            $directory = substr(trim($directory), 0, -1);
        }

        if (is_dir($directory) and is_writeable($directory)) {
            return self::$cacheDir = $directory;
        }

        return false;
    }

    /**
     * Run the script provided, automatically compiling, caching the compiled script, using existing cached files.
     *
     * @param string      $script         JavaScript to be compiled and run.
     * @param null|string $id             Unique ID for this script. If not specified, one will be generated.
     * @param bool        $forceRecompile If true, the script will be re-compiled even if a cached version is found.
     * @param bool        $cacheToFile    If set to false, the script will not be saved to the cache, but WILL still be
     *                                    loaded from cache unless $forceRecompile is also true. Use this for scripts
     *                                    you are sure will only *ever* be run once and don't need caching.
     *
     * @return mixed
     */
    public static function run($script, $id = null, $forceRecompile = false, $cacheToFile = true)
    {
        // Get a unique ID for this script if one was not passed. Test with empty()+is_string() instead of === null
        // to prevent the user from passing an identifier that can't be used as a filename (i.e. object).
        if (empty($id) or !is_string($id)) {
            $id = md5($script);
        }

        self::generateClassFromId($id);

        if (!class_exists(self::getCurrentScriptFQCN()) or ($forceRecompile)) {
            // Script is not loaded OR we need to force a recompile.
            $cached = self::findCachedFile(self::getCurrentScriptClassName());

            if (($cached === false) or $forceRecompile) {
                // No cache, or overridden. Compile the script.
                $php = self::compileScript($script, $id);
                if ($cacheToFile) {
                    $cached = self::cacheCompiledScript(self::getCurrentScriptClassName(), $php);
                }
            }

            // Avoid class collisions caused by $forceRecompile.
            while (class_exists(self::getCurrentScriptFQCN())) {
                self::generateClassFromId(uniqid($id . '_'));
                $cached = false;
            }
            if (self::getCurrentScriptId() !== $id) {
                $php = self::compileScript($script, $id);
            }

            if (($cached === false)) {
                eval($php);
            } else {
                require($cached);
            }
        }

        // In a loop, this is 2-3x faster than call_user_func .. but singly seems to average about the same.
        $object = self::getCurrentScriptFQCN();
        $return = $object::run();
//        $return = call_user_func([self::getCurrentScriptFQCN(), 'run']);

        return self::convertReturnValue($return);
    }

    private static function generateClassFromId($id)
    {
        if (self::$currentScriptId !== $id) {
            self::$currentScriptId = $id;
            self::$currentScriptClassName = self::CLASS_PREFIX . preg_replace(['/-/', '/[^A-Za-z0-9_]+/'], ['_', ''],
                    ucwords($id));
            self::$currentScriptNamespace = __NAMESPACE__;
        }
    }

    /**
     * @return string
     */
    public static function getCurrentScriptFQCN()
    {
        return self::$currentScriptNamespace . '\\' . self::$currentScriptClassName;
    }

    /**
     * @param string $className Class to locate in the cache.
     *
     * @return false|string Returns path and filename if file exists; false otherwise.
     */
    private static function findCachedFile($className)
    {
        //TODO: Provide interface to work with a user's cache system, e.g. one provided by their framework.

        // Attempt to set up basic file caching.
        // If $cacheDir has not been set, set it.
        if (empty(self::$cacheDir)) {
            self::$cacheDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::CLASS_PREFIX . 'cache';
        }

        $filename = self::$cacheDir . DIRECTORY_SEPARATOR . $className . '.cache.php';

        return file_exists($filename) ? $filename : false;
    }

    /**
     * @return string
     */
    public static function getCurrentScriptClassName()
    {
        return self::$currentScriptClassName;
    }

    public static function compileScript($script, $id)
    {
        $php = Compiler::compile($script);

        self::generateClassFromId($id);
        $namespace = self::$currentScriptNamespace;
        $class = self::getCurrentScriptClassName();
        $version = self::VERSION;

        return <<<PHP
<?php
/* Auto-generated cache file for script '$id' */
/* Do not edit this file directly. */
/* Generated by js4php5 $version (http://github.com/hiltonjanfield/js4php5) */

namespace $namespace;

use \js4php5\JS,
    \js4php5\Runtime\Runtime;

class $class {
    $php
}
PHP;
        // No closing PHP tag as per PSR-2
    }

    /**
     * @param string $className Class name for this script.
     * @param string $php       PHP code to place in cache file, as output by JS::compileScript()
     *
     * @return string
     */
    private static function cacheCompiledScript($className, $php)
    {
        if (empty(self::$cacheDir)) {
            $file = self::findCachedFile($className);
            if ($file !== false) {
                unlink($file); // Remove a previously cached file.
            }
        }

        if (!is_dir(self::$cacheDir) and !mkdir(self::$cacheDir, 0777, true)) {
            return false; // Cannot open the chosen cache directory, so cannot cache file.
        }

        $filename = self::$cacheDir . DIRECTORY_SEPARATOR . $className . '.cache.php';

        return (file_put_contents($filename, $php) === false) ? false : $filename;
//        return false; // caching disabled
    }

    /**
     * @return string
     */
    public static function getCurrentScriptId()
    {
        return self::$currentScriptId;
    }

    /**
     * @param Base $o
     *
     * @return mixed
     */
    public static function convertReturnValue($o)
    {
        // Test for null first to safely handle there being no return value.
        if (($o === null) or ($o->type == Base::UNDEFINED) or ($o->type == Base::NULL)) {
            return null;

        } elseif (($o->type == Base::STRING) or ($o->type == Base::NUMBER)) {
            return $o->value;

        } elseif ($o->type == Base::BOOLEAN) {
            return (bool)$o->value;

        } elseif ($o->type == Base::OBJECT) {
            // Can't see any reasonably feasible way to convert Javascript object functions
            $arr = [];
            /**
             * @var string       $key
             * @var jsAttribute $value
             */
            foreach ($o->slots as $key => $value) {
                $arr[$key] = JS::convertReturnValue($value->value);
            }
            return $arr;
//            var_dump($arr);
//            var_dump($o->slots);
//            exit;
//        } elseif ($o->type == Base::REF) {
        } else {
//            throw new jsException(new jsSyntaxError("Unknown script return type: " . $val->type));
        }
    }
}
