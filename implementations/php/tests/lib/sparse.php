<?php

/**
 * Sparse test
 */
use NateFerrero\u\Sparse;
use NateFerrero\u\SparseDocument;

class SparseTest {

    public $parser;

    public function __construct() {
        /**
         * Define a simple grammar for testing, containing
         * a group (in parenthesis) and words, separated by space.
         */
        $this->parser = new Sparse(array(
            'root' => array(
                '&children' => array('group-enter'),
                '&inline' => array('space', 'word')
            ),
            'group-enter' => array(
                '&exit' => array('group-exit')
            ),
            '&tokens' => array(
                'group-enter' => '(',
                'group-exit'  => ')',
                'space'       => '/\s+/',
                'word'       => '/\w+/'
            )
        ));
    }

    public function testTreeGeneration() {
        $doc = $this->parser->apply('', 'root');
        $doc->descend('A');
        $doc->descend('B');
        $doc->descend('C');
        $doc->child('@d');
        $doc->ascend('--c--');
        $doc->child('@c');
        $doc->ascend('--b--');
        $doc->child('@b1');
        $doc->child('@b2');
        $doc->child('@b3');
        $doc->ascend('--a--');

        $tree = $doc->getTree();
        check($tree['token'], 'root');
        check(count($tree['children']), 1);

        $A = $tree['children'][0];
        check($A['match'], 'A');
        check($A['exit'], '--a--');
        check(count($A['children']), 4);

        $B = $A['children'][0];
        check($B['match'], 'B');
        check($B['exit'], '--b--');
        check(count($B['children']), 2);

        $C = $B['children'][0];
        check($C['match'], 'C');
        check($C['exit'], '--c--');
        check(count($C['children']), 1);

        $_c = $B['children'][1];
        check($_c['match'], '@c');
        check(isset($_c['exit']), false);
        check(isset($_c['children']), false);

        $_d = $C['children'][0];
        check($_d['match'], '@d');
        check(isset($_d['exit']), false);
        check(isset($_d['children']), false);

    }

    public function testEmptyDoc() {
        $str = '';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'content' => null
        ));
    }

    public function testWordOnlyDoc() {
        $str = 'battle';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        print_r($tree);die;
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'content' => null
        ));
    }

}
