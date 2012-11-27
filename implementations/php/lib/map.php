<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Map
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;

/**
 * Map
 */
class Map {

    /**
     * Integer keys
     */
    public $ints = array();

    /**
     * String keys
     */
    public $strings = array();

    /**
     * Syntax
     *
    public $syntax = array(
        'root' => array(
            'identifier' => 'root-identifier'
        ),
        'root-identifier' => array(
            '&exec' => array(), 
            '&assign' => 'assignment'
        )
    );*/

    /**
     * Set value
     */
    public function set($key, &$value) {
        if(is_null($key)) {
            array_push($this->ints, $value);
        }
        else if(is_int($key)) {
            $this->ints[$key] = $value;
        }
        else if(is_string($key)) {
            $this->strings[$key] = $value;
        }
        else {
            Runtime::error('map-key-invalid-type', $this, $key);
        }
    }

    /**
     * Get value
     */
    public function get($key) {
        if(is_int($key)) {
            if(!isset($this->ints[$key])) {
                Runtime::error('map-key-not-found', $this, $key);
            }
            return $this->ints[$key];
        }
        else if(is_string($key)) {
            /**
             * Reserved items
             */
            if($key === 'true') {
                return true;
            }

            else if($key === 'false') {
                return false;
            }

            else if($key === 'this') {
                return $this;
            }


            if(!isset($this->strings[$key])) {
                Runtime::error('map-key-not-found', $this, $key);
            }
            return $this->strings[$key];
        }
        else {
            Runtime::error('map-key-invalid-type', $this, $key);
        }
    }

    /**
     * Magic getter
     */
    public function __get($key) {
        return $this->get($key);
    }

    /**
     * Representation
     */
    public function __repr() {
        $keys = array_keys($this->strings);
        if(count($keys) > 10) {
            $keys = array_slice($keys, 0, 10);
            $keys[] = '...';
        }
        return "[". implode(', ', $keys). "]";
    }
}
