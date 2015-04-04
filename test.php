<h1>js4php Test</h1>
<hr>
<pre>
<?php

function __autoload($className)
{
    $path = explode('\\', $className);
    if ($path[0] == 'hiltonjanfield') {
        array_shift($path);
        array_shift($path);
        $className = implode('\\', $path);
    }
    if (file_exists($className . '.php')) {
        require_once($className . '.php');
        return true;
    }
    return false;
}

use js4php5\compiler\jsly;
use js4php5\compiler\lexer\Lexer;
use js4php5\compiler\parser\EasyParser;
use js4php5\JS;
use js4php5\Timer;
use js4php5\VarDumper;

$javascript = <<<'JS'


function calculate(speed) {
    return (speed < 9) ? Math.pow(speed, (10 / 3)) : Math.pow(speed, (10 / 3) + (-0.5 * Math.log(10 - speed) / Math.log(10)));
}

return calculate(9);

JS;

Timer::start('run');
$result = JS::run($javascript);
Timer::stop('run');

VarDumper::dump($result, 'Script Result');

Timer::output_ms();

exit;

/////////////////////////////////////////////


$lex = new Lexer(0, jsly::$lexp);
$parser = new EasyParser(jsly::$dpa);
$timer['Setup'] = microtime(true);

$lex->start($javascript);
$timer['Lexer'] = microtime(true);
try {
    $program = $parser->parse('c_program', $lex);
    $timer['Parser'] = microtime(true);
} catch (\js4php5\compiler\parser\ParseException $e) {
    VarDumper::dump($e, 'Exception');
    exit;
}

$start = $timer[0];
unset($timer[0]);
foreach ($timer as $k => $t) {
    echo $k . ': ' . ($t - $start) . '<br>';
}

$php = $program->emit(true);

VarDumper::dump($php, '$program->emit()');
