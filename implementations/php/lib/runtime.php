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
class HandledException extends \Exception {
    public function __repr() {
        return $this->getMessage();
    }
}

/**
 * Runtime class
 */
class Runtime {

    /**
     * Current version
     */
    public static $version = '0.0.1';

    public static $parser;

    /**
     * Indicate a problem
     */
    public static function error($code, $info = null) {
        $msg = "[u $code";
        if(!is_null($info)) {
            $msg .= ': ' . self::repr($info, true);
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
        $tree = self::parse($str);
        $tree['children'][] = array(
            'token' => 'break', 'match' => null
        );
        $engine = new MapEngine();
        $engine->tree($tree);
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
