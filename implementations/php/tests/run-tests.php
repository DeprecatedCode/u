<?php

/**
 * Run comprehensive tests agains the PHP implementation of u
 */
require_once(__DIR__ . '/../u.php');
$tests = array('lib/sparse' => 'SparseTest');

class AssertionFailure extends Exception {}

function stringize(&$x) {
    if(is_string($x)) {
        $x = "\"$x\"";
    } else if(is_null($x)) {
        $x = 'NULL';
    } else if(is_bool($x)) {
        $x = $x ? 'TRUE' : 'FALSE';
    } else if(is_object($x)) {
        $x = '<' . get_class($x) . '>';
    } else if(is_array($x)) {
        $x = '[ ' . count($x) . ' ]';
    } else {
        $x = '?';
    }
}

function check($a, $b) {
    if($a !== $b) {
        $trace = debug_backtrace();
        $last = array_shift($trace);
        stringize($a);
        stringize($b);
        throw new AssertionFailure("$a is not $b on line $last[line] of $last[file]");
    }
}

echo "\n";

foreach($tests as $key => $value) {
    require_once(__DIR__ . '/' . $key . '.php');
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
        } else if($e instanceof Exception) {
            echo "       " . $e->getMessage() . ' on line ' . $e->getLine() . ' of ' .  $e->getFile();
        } else {
            echo "       " . "Unknown";
        }
        echo "\n\n";
    }
}
