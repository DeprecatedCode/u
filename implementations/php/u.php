<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * PHP Implementation
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;

foreach(explode(' ', 'runtime engine operation sparse grammar expr map') as $file) {
    require_once(__DIR__ . "/lib/$file.php");
}

/**
 * Run the file passed in via the command line
 */
if(isset($argv) && isset($argv[1])) {
    try {
        $result = Runtime::run($argv[1]);
        echo Runtime::repr($result);
    } catch(HandledException $e) {
        echo $e->getMessage() . "\n";
    }
}
