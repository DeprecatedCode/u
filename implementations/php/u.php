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

$version = Runtime::$version;

/**
 * Run the file passed in via the command line
 */
function u($u) {
    echo str_replace('|', "\n  | ", $u);
}
if(isset($argv) && isset($argv[1])) {
    switch($argv[1]) {
        case '-h':
        case '--help':
        case 'help':
        case '?':
            u("|Uncomplicated Programming Language|PHP Implementation|Version $version|");
            u("|Usage|-----||Run a U file:|uphp file/to/run.u|");
            u("|Interactive U shell:|uphp||This help:|uphp --help\n\n");
            break;
        default:
            try {
            $result = Runtime::run($argv[1]);
                echo Runtime::repr($result);
            } catch(HandledException $e) {
                echo $e->getMessage() . "\n";
            }
    }
}

/**
 * Otherwise, run the interpreter
 */
else if(!defined("U_INTERNAL")) {

    function u_exit(&$ctrl_d) {
        echo "\n\n";
        $resp = shell_exec('/bin/bash -c "read -s -e -r -N 1 -p ' .
            '\'u | Exit? (y) \' line && echo \$line"');
        if(is_string($resp)) {
            $resp = trim($resp);
        } else if(is_null($resp)) {
            $ctrl_d = true;
            return true;
        }
        $ctrl_d = false;
        switch($resp) {
            case "":
            case "y":
                return true;
            default:
                return false;
        }
    }

    $engine = new MapEngine();
    u("|Uncomplicated Programming Language|PHP Implementation");
    u("|Version $version|? for help");
    $alt = null;
    while(true) {
        $content = '';
        if(!is_null($alt)) {
            echo $alt;
            $alt = null;
        } else {
            echo "\n\n";
        }

        /**
         * Read a line from the console
         */
        $line = shell_exec('/bin/bash -c "read -e -r -p \'u | \' -i \'' . 
            addslashes(addslashes($content)) . '\' line && echo \$line"');
        $tline = trim($line);
        if($tline === '?') {

            u("|Quick help:|");
            u("| ?                      show this help screen");
            u("| Ctrl+D                 exit the interpreter");
            u("|");
            u("| x: 5                   assign 5 to x in local map");
            u("| x                      show a representation of x");
            u("|");
            u("| # Comment              line comment");
            continue;
        }

        try {
            if($line[strlen($line) -1 ] !== "\n") {
                if(u_exit($ctrl_d)) {
                    if($ctrl_d) {
                        echo "^D\n";
                    }
                    echo "  |";
                    break;
                } else {
                    $alt = "\n";
                    continue;
                }
            }
            $tree = Runtime::parse($line);
            $engine->tree($tree);
            echo "  |\n  | " . Runtime::repr($engine->result);
        } catch(HandledException $e) {
            echo "  |\n  | " . $e->getMessage();
        } catch(Exception $e) {
            echo "FATAL " . $e->getMessage();
        }
    }
    u("|See you next time!\n\n");
}