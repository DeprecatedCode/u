<?php

/**
 * Run comprehensive tests agains the PHP implementation of u
 */
define("U_NON_INTERACTIVE", "yes");
require_once(__DIR__ . '/../u.php');
require_once(__DIR__ . '/lib/common.php');

/**
 * All test cases to run
 */
$tests = array(
    'lib/sparse.php' => 'SparseTest',
    'lib/grammar.php' => 'GrammarTest',
    'lib/engine.php' => 'EngineTest'
);

/**
 * Simple string representation of objects for errors
 */
function stringize($x, $key = null) {
    if(is_string($x)) {
        $out = "\"$x\"";
    } else if(is_null($x)) {
        $out = 'NULL';
    } else if(is_integer($x) || is_float($x)) {
        $out = "$x";
    } else if(is_bool($x)) {
        $out = $x ? 'TRUE' : 'FALSE';
    } else if(is_object($x)) {
        $out = '<' . get_class($x) . '>';
    } else if(is_array($x)) {
        $out = '[';
        foreach($x as $k => $v) {
            $out .= ($out == '[' ? '' : ', ');
            $out .= stringize($v, $k);
        }
        $out = $out . ']';
    } else {
        $out = '?';
    }
    return is_null($key) ? $out : $key . ': ' . $out;
}

/**
 * Simple assertion
 */
class AssertionFailure extends Exception {}

function check($a, $b) {
    if($a !== $b) {
        $trace = debug_backtrace();
        $last = array_shift($trace);
        $a = stringize($a);
        $b = stringize($b);
        throw new AssertionFailure("$a is not $b on line $last[line] of $last[file]");
    }
}

echo "\n";

/**
 * Run all tests
 */
foreach($tests as $key => $value) {
    require_once(__DIR__ . '/' . $key);
    echo $value . "\n    ";
    $list = array();
    foreach(get_class_methods($value) as $method) {
        try {
            $instance = new $value;
            $status = $instance->$method();
            echo '.';
        } catch(AssertionFailure $e) {
            echo 'F';
            $list[$method] = $e;
        } catch(Exception $e) {
            echo 'E';
            $list[$method] = $e;
        }
    }
    echo "\n\n";
    $i = 0;
    if(count($list) > 0) {
        echo "    Failures:\n\n";
    }
    foreach($list as $method => $e) {
        $i++;
        echo "    $i) ${value}->${method}()\n";
        if($e instanceof AssertionFailure) {
            echo "       " . $e->getMessage();
        } else if($e instanceof NateFerrero\u\HandledException) {
            echo "       " . $e->getMessage();
        } else if($e instanceof Exception) {
            echo "       " . $e->getMessage() . ' on line ' . $e->getLine() . ' of ' .  $e->getFile();
        } else {
            echo "       " . "Unknown";
        }
        echo "\n\n";
    }
}
