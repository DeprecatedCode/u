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
        return new SparseDocument($this->grammar, $str, $context);
    }
}

/**
 * Sparse document
 */
class SparseDocument {

    private $pointer = 0;
    private $line = 0;
    private $col = 0;
    private $length = 0;
    private $str;
    private $tokens;
    private $grammar;
    private $tree;
    private $tip;
    private $stack = array();
    private $context;

    public function __construct(&$grammar, &$str, $context) {
        $this->grammar = $grammar;
        $this->str = $str;
        $this->length = strlen($str);
        $this->tokens = $grammar['&tokens'];
        $this->context = $context;
        $tmp = $this->node(null);;
        $this->tree = &$tmp;
        $this->tip = &$tmp;
    }

    /**
     * Get the tree
     */
    public function getTree() {
        return $this->tree;
    }

    /**
     * Create a node
     */
    public function node($context, $match) {
        if(!isset($this->grammar[$context])) {
            $this->fail("undefined-token: $context");
        }
        return array(
            'token' => $context,
            'match' => $match,
            'content' => null
        );
    }

    /**
     * Create a child of the tip
     */
    public function &child($context, $match) {
        $tmp = $this->node($context, $match);
        if(!isset($this->tip['children'])) {
            $this->tip['children'] = array();
        }
        $this->tip['children'][] = &$tmp;
        return $tmp;
    }

    /**
     * Drop into a child token
     */
    public function descend($context, $match) {
        $this->stack[] = &$this->tip;
        $tmp = &$this->child($context, $match);
        unset($this->tip);  # Break ref
        $this->tip = &$tmp;
        $this->context = $context;
    }

    /**
     * Pop up out of a child token
     */
    public function ascend($match) {
        $this->tip['exit'] = $match;
        $tmp = &$this->stack[count($this->stack) - 1];
        array_pop($this->stack);
        unset($this->tip);  # Break ref
        $this->tip = &$tmp;
        if(!is_array($this->tip)) {
            $this->fail("cannot-ascend");
        }
        $this->context = $this->tip['token'];
    }

    /**
     * Tokenize
     */
    public function &tokenize() {

        while($this->pointer < $this->length) {
            /**
             * Current context instructions
             */
            $scope = $this->grammar[$this->context];

            /**
             * Handle children first
             */
            if(isset($scope['&children'])) {
                foreach($scope['&children'] as $def) {
                   echo 'x';
                }
            }

            /**
             * Handle inline tokens
             */
            if(isset($scope['&inline'])) {
                foreach($scope['&inline'] as $def) {
                   $match = $this->attempt($def);
                   if(!is_null($match)) {
                        $this->child($match, )
                   }
                }
            }
            $this->fail("no-match");
        }

        return $this->tree;;
    }

    /**
     * How many characters to show near a failure
     */
    const FAIL_NEAR_LENGTH = 20;

    /**
     * Throw an exception
     */
    private function fail($why) {
        $near = substr($this->str, $this->pointer);
        if(strlen($near) > self::FAIL_NEAR_LENGTH) {
            $near = substr($near, 0, self::FAIL_NEAR_LENGTH) . '...';
        }
        throw new ParseException(
            $this->line,
            $this->col,
            $why,
            $near
        );
    }
}
