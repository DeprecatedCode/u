<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Map
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;
use NateFerrero\u\Runtime;

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
     * Set value
     */
    public function set($key, &$value) {
        if(is_null($key)) {
            array_push($this->ints, $value);
        }
        else if(is_int($key)) {
            $this->ints[$key] = $value;
        }
        else if(is_str($key)) {
            $this->strings[$key] = $value;
        }
        else {
            Runtime::error('map-key-invalid-type', $this, $key);
        }
    }

    /**
     * Get value
     */
    public function get($key, &$value) {
        if(is_int($key)) {
            if(!isset($this->ints[$key])) {
                Runtime::error('map-key-not-found', $this, $key);
            }
            return $this->ints[$key];
        }
        else if(is_str($key)) {
            if(!isset($this->strings[$key])) {
                Runtime::error('map-key-not-found', $this, $key);
            }
            return $this->strings[$key];
        }
        else {
            Runtime::error('map-key-invalid-type', $this, $key);
        }
    }
}
