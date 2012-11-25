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

	public $map;

	/**
	 * Constructor
	 */
	public function __construct(&$tree) {
		$this->map = new Map();
		$this->context = &$this->map;
		$this->token($tree);
	}

	/**
	 * Input
	 */
	public function token($token) {
		switch($token['token']) {
			case 'space':
			case 'break':
				break;
			default:
				var_dump($token['token']);
				var_dump($token['match']);
		}
		if(isset($token['children'])) {
			foreach($token['children'] as $next) {
				$this->token($next);
			}
		}
	}
}
