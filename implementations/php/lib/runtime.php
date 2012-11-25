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
 * Handled Exception
 */
class HandledException extends \Exception {}

/**
 * Runtime class
 */
class Runtime {
    public static $parser;

    /**
     * Indicate a problem
     */
    public static function error($code, $context, $object = null) {
        echo '[u error] ' . $code;
        if(!is_null($context)) {
            echo ': ' . self::repr($context);
        }
        if(!is_null($object)) {
            echo ': ' . self::repr($object);
        }
        echo "\n";
        throw new HandledException;
    }

    /**
     * Representation of an object
     */
    public static function repr(&$var) {
        if(is_null($var)) {
            return "void";
        }
        if(is_bool($var)) {
            return $var ? "true" : "false";
        }
        if(is_int($var) || is_float($var)) {
            return "$var";
        }
        if(is_string($var)) {
            return "\"$var\"";
        }
        if(is_object($var)) {
            if(method_exists($var, '__repr')) {
                return $var->__repr($var);
            }
        }
        return '< ? >';
    }

    /**
     * Execute a u program from a file
     */
    public static function run($file) {
        try {
            if(!is_file($file)) {
                self::error('file-not-found', $file);
            }
            return self::exec(file_get_contents($file));
        } catch(HandledException $e) {
            # Do nothing
        }
    }

    /**
     * Execute a u program from a string
     */
    public static function exec($str) {
        try {
            $tokens = self::parse($str);
            echo json_encode($tokens);die;
        } catch(HandledException $e) {
            # Do nothing
        }
    }

    /**
     * Generate tokens from a string
     */
    public static function parse($str) {
        try {
            return self::$parser->apply($str, 'root')->tokenize();
        } catch(ParseException $e) {
            self::error('parse-error', $e->getMessage());
        } catch(HandledException $e) {
            # Do nothing
        }
    }
}
