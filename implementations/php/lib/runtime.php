<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Runtime
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;

/**
 * Runtime class
 */
class Runtime {
    public static $parser;

    /**
     * Indicate a problem
     */
    public static function error($code, $context, $object = null) {
        echo '[u error] ' . $code; die;
    }

    /**
     * Execute a u program from a file
     */
    public static function run($file) {
        if(!is_file($file)) {
            self::error('file-not-found', $file);
        }
        return self::exec(file_get_contents($file));
    }

    /**
     * Execute a u program from a string
     */
    public static function exec($str) {
        $tokens = self::$parser->apply($str);
        print_r($tokens);die;
    }
}
