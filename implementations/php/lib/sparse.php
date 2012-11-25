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
    private $line = 1;
    private $col = 1;
    private $_pointer = 0;
    private $_line = 1;
    private $_col = 1;
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
        $tmp = $this->node($context, null);;
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
    public function node($token, $match) {
        return array(
            'token' => $token,
            'match' => $match
        );
    }

    /**
     * Create a child of the tip
     */
    public function &child($token, $match) {
        $tmp = $this->node($token, $match);
        if(!isset($this->tip['children'])) {
            $this->tip['children'] = array();
        }
        $this->tip['children'][] = &$tmp;
        return $tmp;
    }

    /**
     * Drop into a child token
     */
    public function descend($token, $match) {
        if(!isset($this->grammar[$token])) {
            $this->fail("invalid-context: $token");
        }
        $this->stack[] = &$this->tip;
        $tmp = &$this->child($token, $match);
        unset($this->tip);  # Break ref
        $this->tip = &$tmp;
        $this->context = $token;
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
            $scope = &$this->grammar[$this->context];

            /**
             * Prefetch exit condition in case of context jump
             */
            $exit = isset($scope['&exit']) ? $scope['&exit'] : null;
            $absorb = false;
            if(isset($scope['&exit+'])) {
                $exit = $scope['&exit+'];
                $absorb = true;
            }

            /**
             * Whether or not to absorb non-matching chars
             */
            $literal = false;

            /**
             * Jump to context with content
             */
            if(isset($scope['&content'])) {
                $content = $scope['&content'];
                if($content === '&literal') {
                    $literal = true;
                } else {
                    if(!isset($this->grammar[$content])) {
                        $this->fail("invalid-content: $content");
                    }
                    $scope = &$this->grammar[$content];  # Process as if this were $content
                }
            }

            /**
             * Handle children first
             */
            if(isset($scope['&children'])) {
                foreach($scope['&children'] as $token) {
                    $match = $this->attempt($token);
                    if(!is_null($match)) {
                        $this->descend($token, $match);
                        continue 2;  # Match found, progress forward
                    }
                }
            }

            /**
             * Handle inline tokens
             */
            if(isset($scope['&inline'])) {
                foreach($scope['&inline'] as $token) {
                    $match = $this->attempt($token);
                    if(!is_null($match)) {
                        $this->child($token, $match);
                        continue 2;  # Match found, progress forward
                    }
                }
            }

            /**
             * Handle exit tokens
             */
            if(!is_null($exit)) {
                if(!is_array($exit)) {
                    $exit = array($exit);
                }
                foreach($exit as $token) {
                    $match = $this->attempt($token);
                    if(!is_null($match)) {
                        /**
                         * Absorb right-content (exit)
                         */
                        if($literal && $absorb) {
                            $this->tip['content'] .= $match;
                        }
                        $this->ascend($match);
                        continue 2;  # Match found, progress forward
                    }
                }
            }

            /**
             * Check literal flag and absorb a single character at a time
             */
            if($literal) {
                $char = $this->str[$this->pointer];
                $this->tip['content'] .= $char;
                $this->advance($char);
                continue;
            }
            $this->fail("no-match: $this->context");
        }

        return $this->tree;;
    }

    /**
     * Attempt to match a token
     */
    public function attempt($token) {
        if(!isset($this->tokens[$token])) {
            $this->fail("invalid-token: $token");
        }
        return $this->match($this->tokens[$token]);
    }

    /**
     * Match a value
     */
    public function match($value) {
        /**
         * Handle multiple matches per token
         */
        if(is_array($value)) {
            foreach($value as $v) {
                $match = $this->match($v);
                if(!is_null($match)) {
                    return $match;
                }
            }
            return null;
        }

        /**
         * Verify type
         */
        if(!is_string($value) || $value === '') {
            $this->fail("bad-token-type");
        }

        $len = strlen($value);

        /**
         * Match a regular expression
         */
        if($len > 2 && $value[0] === '/' && $value[$len - 1] === '/') {
            $match = preg_match($value . 'A', $this->str, $matches,  # A = Anchored
                PREG_OFFSET_CAPTURE, $this->pointer);

            /**
             * Must match at the current pointer position
             */
            if($match === 1 && $matches[0][1] === $this->pointer) {
                $match = $matches[0][0];
                $this->advance($match);
                return $match;
            }
        }

        /**
         * Match a literal string
         */
        else if(substr($this->str, $this->pointer, $len) === $value) {
            $this->advance($value);
            return $value;
        }

        /**
         * No match
         */
        return null;
    }

    /**
     * Advance the pointer by string
     */
    public function advance($str) {
        $this->_pointer = $this->pointer;
        $this->_line = $this->line;
        $this->_col = $this->col;
        $len = strlen($str);
        $this->pointer += $len;
        for($i = 0; $i < $len; $i++) {
            if($str[$i] == "\r") {
                if(isset($str[$i + 1]) && $str[$i + 1] == "\n") {
                    $i++;
                }
                $this->line++;
                $this->col = 1;
            }
            else if($str[$i] == "\n") {
                $this->line++;
                $this->col = 1;
            } else {
                $this->col++;
            }
        }
    }

    /**
     * How many characters to show near a failure
     */
    const FAIL_NEAR_LENGTH = 20;

    /**
     * Throw an exception
     */
    private function fail($why) {
        $near = substr($this->str, $this->_pointer);
        if(strlen($near) > self::FAIL_NEAR_LENGTH) {
            $near = substr($near, 0, self::FAIL_NEAR_LENGTH) . '...';
        }
        throw new ParseException(
            $this->_line,
            $this->_col,
            $why,
            $near
        );
    }
}
