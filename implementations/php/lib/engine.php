<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Engine
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;

/**
 * Engine - process a single u file
 */
class Engine {

    /**
     * Apply another tree
     */
    public function tree(&$tree) {
        if(isset($tree['children'])) {
            foreach($tree['children'] as $token) {
                $this->token($token);
            }
        }
    }

}

/**
 * Engine for applying tokens in a Map
 */
class MapEngine extends Engine {
    public $map;
    public $key = null;
    public $value = null;
    public $result = null;
    public $operations = array();
    public $seek = false;

    /**
     * Initialize
     */
    public function __construct() {
        $this->map = new Map();
    }

    /**
     * Update value
     */
    public function value($value) {
        /**
         * Reduce two values with an operation
         */
        if(count($this->operations) > 0 || !is_null($this->value)) {
            $this->value = operation($this->value, $value, $this->operations);
            $this->operations = array();
        }

        else {
            $this->value = $value;
        }
    }

    /**
     * Input into MapEngine
     */
    public function token($token) {

        switch($token['token']) {

            /**
             * Whitespace / comments
             */
            case 'space':
            case 'comment':
            case 'comment-[':
                break;

            /**
             * Line break or comma
             */
            case 'break':
            case 'sep':
                if(count($this->operations) > 0) {
                    Runtime::error("trailing-operation", $this->operation);
                }
                /**
                 * Autoincrement keys when null
                 */
                if(is_null($this->key)) {
                    $this->key = count($this->map->ints);
                }
                $this->map->set($this->key, $this->value);
                $this->key = null;
                $this->result = $this->value;
                $this->value = null;
                $this->seek = false;
                return;

            /**
             * Colon, prepare key (seek = true)
             */
            case 'colon':
                $this->key = $this->value;
                $this->value = null;
                $this->seek = true;
                return;

            /**
             * Strings
             */
            case 'str-1-s':
            case 'str-1-d':
            case 'str-3-s':
            case 'str-3-d':
                $engine = new StringEngine();
                $engine->tree($token);
                return $this->value($engine->value);

            /**
             * Identifier
             */
            case 'identifier':
                if($this->seek) {
                    return $this->value($this->map->get($token['match']));
                } else {
                    return $this->value($token['match']);
                }

            /**
             * Int
             */
            case 'int':
                return $this->value((int) $token['match']);

            /**
             * Float
             */
            case 'float':
                return $this->value((float) $token['match']);

            /**
             * Operator
             */
            case 'operator':
                $this->operations[] = $token['match'];
                return;

            /**
             * Unknown 
             */
            default:
                echo "FIX: ";
                var_dump($token['token']);
                var_dump($token['match']);
        }
    }
}

/**
 * String engine
 */
class StringEngine extends Engine {
    public $value = '';
    public function token($token) {
        switch($token['token']) {
            case 'escape':
                $this->value .= $this->unescape($token['match']);
                break;
            case '&literal':
                $this->value .= $token['match'];
            default:
                Runtime::error('invalid-string-token', $token['token']);
        }
    }
    public function unescape($x) {
        switch($x) {
            case '\\n':
                return "\n";
            case '\\r':
                return "\r";
            case '\\t':
                return "\t";
            case '\\\\':
                return "\\";
            case "\\'":
                return "'";
            case '\\"':
                return '"';
            default:
                Runtime::error('string-unknown-escape-sequence', $x);
        }
    } 
}
