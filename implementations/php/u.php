<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * PHP Implementation
 *
 * @author Nate Ferrero
 */

/* 1 */ require_once('lib/runtime.php');
/* 2 */ require_once('lib/sparse.php');
/* 3 */ require_once('lib/grammar.php');
/* 4 */ require_once('lib/expr.php');
/* 5 */ require_once('lib/map.php');

/**
 * Run the file passed in via the command line
 */
if(isset($argv) && isset($argv[1])) {
    NateFerrero\u\Runtime::run($argv[1]);
}
