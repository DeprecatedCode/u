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
 * Engine for applying tokens in a Map Context
 */
class MapEngine extends Engine {
    public $map;
    public $queue = array();

    /**
     * Initialize
     */
    public function __construct() {
        $this->map = new Map();
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
                return;

            /**
             * Line break or comma triggers execution
             */
            case 'break':
            case 'sep':
                $queue = $this->queue;
                $this->queue = array();
                var_dump($queue);
                try {
                    $this->result = $this->flush($queue);
                } catch(HandledException $e) {
                    $this->result = $e;
                }
                return;

            /**
             * Queue
             */
            default:
                $this->queue[] = $token;
                return;
        }
    }

    /**
     * Execute and flush the command queue
     */
    public function flush($queue) {
        /**
         * Check for solo get
         */
        $identifier = isset($queue[0])
            && $queue[0]['token'] == 'identifier';

        /**
         * Check for set
         */
        $cpos = $identifier ? 1 : 0;
        $colon = isset($queue[$cpos])
            && $queue[$cpos]['token'] == 'colon';

        $val = null;

        /**
         * Process solo get
         */
        if(count($queue) == 1 && $identifier) {
            $val = $this->map->get($queue[0]['match']);
        } else {
            if($colon) {
                /**
                 * Set value in map
                 */
                if($identifier) {
                    $set = array_shift($queue);
                    $set = $set['match'];
                } else {
                    $set = null;
                }

                /**
                 * Remove colon from queue
                 */
                array_shift($queue);
            } else {
                $set = false;
            }

            /**
             * Evaluate expression
             */
            $engine = new ExprEngine();
            $tree = array('children' => $queue);
            $engine->tree($tree);
            $val = $engine->evaluate();

            /**
             * Set value if requested
             */
            if($set !== false) {
                $this->map->set($set, $val);
            }
        }
        return $val;
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
                break;
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

/**
 * Expression engine
 */
class ExprEngine extends Engine {
    public function token($token) {
        switch($token['token']) {
            default:
                Runtime::error('expr-invalid-token', $token['token'], $token['match']);
        }
    }
    public function evaluate() {
        return 5;
    }
}