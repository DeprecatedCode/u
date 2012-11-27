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
    switch($argv[1]) {
        case '-v':
        case '--version':
        case '-h':
        case '--help':
        case 'help':
        case '?':
            define('U_MESSAGE', 'help');
            require_once(__DIR__ . '/lib/shell.php');
            break;
        default:
            try {
                $result = Runtime::run($argv[1]);
                echo Runtime::repr($result);
            } catch(HandledException $e) {
                echo $e->getMessage() . "\n";
            } catch(\Exception $e) {
                echo "FATAL " . $e->getMessage() . "\n";
            }
    }
}

/**
 * Otherwise, run the interpreter
 */
else if(!defined("U_NON_INTERACTIVE")) {
    require_once(__DIR__ . "/lib/shell.php");
}