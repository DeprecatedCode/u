<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Operation
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;

/**
 * Operation - reduce two values
 */
function operation(&$a, &$b, $ops) {

	/**
	 * Handle multiple operators
	 */
	while(count($ops) > 1) {
		$op = array_pop($ops);

		/**
		 * Negation
		 */
		if($op === '-') {
			if(!is_integer($b) && !is_float($b)) {
				Runtime::error("non-numeric-right-operand", $op, $b);
			}
			$b = -$b;
		}

		/**
		 * Boolean negation
		 */
		else if($op === '!') {
			if(!is_bool($b)) {
				Runtime::error("non-boolean-right-operand", $op, $b);
			}
			$b = !$b;
		}

		/**
		 * Error
		 */
		else {
			Runtime::error("invalid-operator-sequence", $op);
		}
	}

	/**
	 * Apply final operator
	 */
	$op = array_pop($ops);
	switch($op) {
		case null:
			if(is_object($a) && method_exists($a, '__apply')) {
				return $a->__apply($b);
			} else {
				Runtime::error('invalid-left-operand', $a);
			}

		/**
		 * Boolean operations
		 */
		case '!':

			if(!is_null($a)) {
				Runtime::error("invalid-left-operand", $op, $a);
			}

			if(!is_bool($b)) {
				Runtime::error("non-boolean-right-operand", $op, $b);
			}

			return !$b;

		case '&&':
		case '||':

			if(!is_bool($a)) {
				Runtime::error("non-boolean-left-operand", $op, $a);
			}

			if(!is_bool($b)) {
				Runtime::error("non-boolean-right-operand", $op, $b);
			}

			switch($op) {
				case '&&':
					return $a && $b;
				case '||':
					return $a || $b;
			}

		/**
		 * Comparison operations
		 */
		case '==':
		case '!=':
		case '>=':
		case '<=':
		case '>':
		case '<':
			if(is_integer($a) || is_float($a)) {
				if(is_integer($b) || is_float($b)) {
					switch($op) {
						case '==':
							return $a == $b;
						case '!=':
							return $a != $b;
						case '>=':
							return $a >= $b;
						case '<=':
							return $a <= $b;
						case '>':
							return $a > $b;
						case '<':
							return $a < $b;
					}
				} else {
					Runtime::error('uncomparable-right-operand', $b);
				}
			}
			if(is_object($a) && method_exists($a, '__compare')) {
				return $a->__compare($b);
			} else {
				
			}
			Runtime::error('uncomparable-left-operand', $a);

			switch($op) {
				case '==':
				case '!=':
				case '>=':
				case '<=':
				case '>':
				case '<':
			}



		/**
		 * Math operations
		 */
		case '+':
		case '-':
		case '*':
		case '/':
		case '%':
		case '^':
			if(($op === '+' || $op === '-') && is_null($a)) {
				$a = 0;  # Allow +3.4 or -300 etc.
			}

			if(!is_integer($a) && !is_float($a)) {
				Runtime::error("non-numeric-left-operand", $op, $a);
			}
			
			if(!is_integer($b) && !is_float($b)) {
				Runtime::error("non-numeric-right-operand", $op, $b);
			}

			switch($op) {
				case '+':
					return $a + $b;
				case '-':
					return $a - $b;
				case '*':
					return $a * $b;
				case '/':
					return $a / $b;
				case '%':
					return $a % $b;
				case '^':
					return pow($a, $b);
			}
		default:
			Runtime::error('invalid-operator', $op);
	}
}