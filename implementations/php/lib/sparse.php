<?php

/**
 * u :: Uncomplicated Programming Language
 *
 * Sparse
 *
 * @author Nate Ferrero
 */
namespace NateFerrero\u;
use NateFerrero\u\Runtime;

/**
 * ParseException
 */
class ParseException extends \Exception {
    public $_line;
    public $_col;
    public $_code;
    public $_info;
    public function __construct($line, $col, $code, $info = null) {
        $this->_line = $line;
        $this->_col = $col;
        $this->_code = $code;
        $this->_info = $info;
        parent::__construct("$this->_code on line $this->_line at column $this->_col: $this->_info");
    }
}

/**
 * Sparse
 */
class Sparse {

    private $grammar = array();

    public function __construct($grammar) {
        $this->grammar = $grammar;
    }

    /**
     * Apply the grammar to a string, generating an array of tokens
     */
    public function apply($str, $context='root') {
        $pointer = 0;
        $line = 0;
        $col = 0;
        $tokens = array();
        $stack = &$tokens;
        $length = strlen($str);

        while($pointer < $length) {

            if(!isset($this->grammar[$context])) {
                throw new ParseException($line, $col, "invalid-context", $context);
            }

            foreach($this->grammar[$context] as $token => $def) {

            }
            
            throw new ParseException($line, $col, "no-match", substr($str, $pointer));
        }

        return $tokens;
    }
}
