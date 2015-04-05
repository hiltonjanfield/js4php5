# js4php5

## What is this?

js4php5 is an ECMAscript/JavaScript interpreter, as defined by Ecma-262 3rd edition. It is *functional*, if slow and incomplete.

This project is an indirect fork of the [j4p5 bundle for Symfony](https://github.com/walterra/j4p5bundle),
which in itself is based off of [j4p5 1.1](http://j4p5.sourceforge.net/). This project was originally started to modify and update j4p5 for use in the [StarsNT project](https://github.com/hiltonjanfield/starsnt). As the code is potentially useful for others, it was decided not to strip any functionality, but rather to clean up and modernize the code.

See the **Information** and **Bugs** sections below for more details.

## Basic Usage

Terms:

- The *source script* is the original JavaScript/EcmaScript to be run.
- A *compiled script* is one that has been translated into PHP, ready to be run.
- A *loaded script* is resident in memory. js4php5 turns source scripts into PHP classes. Once loaded, PHP classes remain available in memory until the end of the session.
- A *cached script* is one that has been stored on disk.

Basic usage of the project is simply to call `JS::run()`.
Example:
```php
JS::run('var js4php5 = "It works!"; print(js4php5);');
```

Most of the other classes are not intended for direct usage, outside of what is documented here.

---

```php
JS::run($script, [$id = null, [$forceRecompile = false, [$cacheToFile = true]]);
```
Compile, cache, and execute the script. Use already-loaded or cached versions if they exist, unless `$forceRecompile` is true.

Parameters:

- `$script` - the JavaScript code to execute.
- `$id` - Script ID to use when referencing, and as part of the class name and cache filename. If `null`, an ID will be generated using `md5($script)`.
- `$forceRecompile` - If true, the given script will be compiled even if a cached version is found.
- `$cacheToFile` - If true, the compiled script will be cached to file. If `$forceRecompile` is also true, the newly compiled script will replace the previous one in the cache.

Returns the value returned by the script, converted to a standard PHP value.

```
JS::getCurrentScriptId()
```
Returns the ID of the last run script. Use this to get the ID of a script for which you did not provide your own ID.

```
JS::getCurrentScriptFQCN()
```
Returns the Fully Qualified Class Name of the last run script.
Once a script has been run, the class remains in memory until the end of the session.

`$myscript = JS::getCurrentScriptFQCN();` will grab the script reference so you can run it again at any time using `$myscript::run();`. However, there is very little overhead simply using JS::run('', 'myScriptID'); after the first call.

For those thinking about using js4php5 to run small scripts/functions repeatedly, as is our main use case in StarsNT: Testing shows that every time the object is called, it gets faster. Significantly faster after the first (loading and/or compiling) call, obviously, but continued speed increases occur on stock PHP installs (no code caching system). Tests on the dev machine have results along the lines of `[250ms, 40ms, 25ms, 18ms, 9ms]` when a small script is called five times.

```
JS::callFunction($name, array $parameters = [])
```
Function which calls a Javascript function from a loaded script. **Use with caution; only works on functions defined in the most recently loaded script!**
`$myscript::callFunction('myfunctionname', [17, 'parameters', 'here']);`.

```
JS::convertReturnValue($value)
```
If you are calling a script manually as noted above (`$myscript::run()`) and need the return value of the script, you can use this function to convert it to a usable PHP value (`$result = JS::convertReturnValue($myscript::run());`).
This function is automatically called by JS::run() and Runtime::callFunction() before a value is returned.

Parameters:

- `$value` - JavaScript returned object to convert to a PHP value.

```
JS::defineObject($objectName, [$functions = null, [$variables = null]])
```
Define your own JavaScript object.
$functions is an array consisting of 'javascriptFunctionName' => 'PHPFunctionName'.
  Named PHP functions must exist in the global namespace.
$variables is an array consisting of 'javascriptVariableName' => value.
  Values must be standard values; no objects or arrays. If you require this functionality,
  use the Runtime:: API directly.
Example:
```
  JS::defineObject('external', ['sha1' => 'js_sha1', 'add' => 'js_add'], ['PI' => 3.14159]);
```
This creates a JavaScript object called `external` with:
- external.sha1(...)
- external.add(...)
- external.PI

```
JS::setCacheDir($directory);
```
Set the directory where cached files are kept.
Returns FALSE if the directory is invalid or cannot be written to.
If not called, the cache dir defaults to the temporary directory reported by PHP (sys_get_temp_dir()).


## Information

The intent:
- Clean up the code.
    - Use a modern code style guide (PSR-2).
    - Clear variable names.
    - Add PHPdocs everywhere to help with understanding the code and hinting for modern IDEs.
- Update the code to make use of newer features (PHP 5.4+) where speedups can be made.
- To fix bugs and make improvements as needed.
- To make the code fully usable by everybody - not just as a Symfony bundle.

Things we've done prior to the initial github upload:
- Changed license from GPL to MIT.
- PHPdocs. A lot of PHPdocs. But not nearly enough.
- Pretty much completely rewritten the main JS wrapper class.
- Namespaced all the things, and moved all the classes to appropriate namespaces.
- Renamed all construct classes c_* to differentiate from the original runtime\js_* classes (now camelCased js* names).
- Code clarity: Added expected parameter lists to thin wrapper classes that simply used func_get_args() in the constructor, and hoping like hell I got them all right.
- Code clarity: Removed lazy list() calls in constructors.
- Code clarity: Renamed parameters, i.e. $a -> $arguments, $w -> $unusedParameter
- Code clarity: Changed all emit() functions to use true booleans rather than 0/1.
- Added className() function to compiler\constructs\BaseConstruct.
- Changed all former js::get_class_name_without_namespace() calls to instanceof comparisons and ::className() calls as necessary.
- Stored current script class name in JS::, added getters.
- Removed output class; the two changes above rendered it unneeded.
- Fixed and optimized imports.
- Formatted all code.
- Called script can now return values to PHP ($result = JS::run($script)) rather than having to pluck values out after the script has been executed.
- Stole and slightly modified Yii2's VarDumper class to assist in development. Sorry @qiangxue! We'll remove it sooner or later.
- Added a simple Timer class to assist in development. To be removed later.

## Todo

Things left to do:
- Massive amounts of bug testing to see what is still broken.
- Implement unit tests, re the above.
- Add support for user-supplied caching (i.e. using their framework's cache system). The current cache simply makes use of a `js4php5` directory under the system's temporary directory.
- A lot of cleanup and possible improvement. See TODO entries throughout the code.
- The majority of parser\ and lexer\ have not been reviewed or commented.
- grammar\ has not been added (files in j4p5: generator.so.php, metascanner.y, js.l, js.y) and is not necessary to simply run scripts. It would be needed to extend the grammar file (generates jsly.php).

## Bugs

Notes from original j4p5:
- Slow. The Lexer has some sloppily designed matching logic, and the runtime is object-happy.
- Bug: No support for "magic semicolons".
- Bug: No Unicode support.
- Bug: Line terminators are currently allowed in string literals.
- Bug: No /regexp/ literals. RegExp(s) objects are not implemented either, but stubs are there.
- Bug: Various deviations from a "pure" ecma-262 grammar. Might not be worth fixing as long as standard scripts run.
- Bug: Various shortcuts in standard functions implementations.
- Bug: No function(). May or may not be worth implementing.
- Bug: No eval(), although it is likely not worth implementing.

Newly noted bugs:
- Bug: + (and likely other operators) at the end of a script causes a semi-infinite loop in the parser rather than a syntax error.
- Bug: JavaScript [array] return values are not working; only last value is returned. However OBJECTS work fine.
