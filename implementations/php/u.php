<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * PHP Implementation
 *
 * @author Nate Ferrero
 */
foreach(explode(' ', 'runtime sparse grammar expr map') as $file) {
    require_once(__DIR__ . "/lib/$file.php");
}

/**
 * Run the file passed in via the command line
 */
if(isset($argv) && isset($argv[1])) {
    NateFerrero\u\Runtime::run($argv[1]);
}
