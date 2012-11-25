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
        $msg = "[u $code";
        if(!is_null($context)) {
            $msg .= ': ' . self::repr($context, true);
        }
        if(!is_null($object)) {
            $msg .= ': ' . self::repr($object);
        }
        $msg .= "]";
        throw new HandledException($msg);
    }

    /**
     * Representation of an object
     */
    public static function repr(&$var, $strpass = false) {
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
            if($strpass) {
                return $var;
            }
            return "\"$var\"";
        }
        if(is_object($var)) {
            if(method_exists($var, '__repr')) {
                return $var->__repr($var);
            }
        }
        return '?';
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
        $engine = new Engine(self::parse($str));
        return $engine->map;
    }

    /**
     * Generate tokens from a string
     */
    public static function parse($str) {
        try {
            return self::$parser->apply($str, 'root')->tokenize();
        } catch(ParseException $e) {
            self::error('parse-error', $e->getMessage());
        }
    }
}
