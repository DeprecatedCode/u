<?php

/**
 * Run comprehensive tests agains the PHP implementation of u
 */
require_once(__DIR__ . '/../u.php');
$tests = array('lib/sparse' => 'SparseTest');

foreach($tests as $key => $value) {
    require_once(__DIR__ . '/' . $key . '.php');
    echo $value . "\n    [";
    $list = array();
    foreach(get_class_methods($value) as $method) {
        try {
            $instance = new $value;
            $status = $instance->$method();
            echo $status ? '.' : 'F';
            if($status !== true) {
                $list[$method] = false;
            }
        } catch(Exception $e) {
            echo 'E';
            $list[$method] = $e;
        }
    }
    echo "]\n\n";
    $i = 0;
    foreach($list as $method => $err) {
        $i++;
        echo "    $i) ${value}->${method}()";
        if($err === false) {
            echo "          Returned non-true value";
        } else {
            echo "          " . $e->getMessage() . ' on line ' . $e->getLine() . ' of ' .  $e->getFile();
        }
        echo "\n\n";
    }
}
