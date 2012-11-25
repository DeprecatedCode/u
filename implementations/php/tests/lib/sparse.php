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
                '&content' => 'root',
                '&exit' => array('group-exit')
            ),
            'string' => array(
                '&content' => '&literal',    throw new Exception("TEST LITERAL", 1);
                
                '&exit' => array('string')
            ),
            '&tokens' => array(
                'group-enter' => '(',
                'group-exit'  => ')',
                'space'       => '/\s+/',
                'word'        => '/\w+/',
                'string'      => '"'
            )
        ));
    }

    public function testTreeGeneration() {
        $doc = $this->parser->apply('', 'root');
        $doc->descend('root', 'A');
        $doc->descend('root', 'B');
        $doc->descend('root', 'C');
        $doc->child('root', '@d');
        $doc->ascend('--c--');
        $doc->child('root', '@c');
        $doc->ascend('--b--');
        $doc->child('root', '@b1');
        $doc->child('root', '@b2');
        $doc->child('root', '@b3');
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
        ));
    }

    public function testWordOnlyDoc() {
        $str = 'battle';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(array(
                'token' => 'word',
                'match' => 'battle',
            ))
        ));
    }

    public function testSpaceOnlyDoc() {
        $str = '     ';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(array(
                'token' => 'space',
                'match' => '     ',
            ))
        ));
    }

    public function testInlineOnlyDoc() {
        $str = ' battle  of   the  ages ';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(
                space_arr(1),
                word_arr('battle'),    # battle
                space_arr(2),
                word_arr('of'),        # of
                space_arr(3),
                word_arr('the'),       # the
                space_arr(2),
                word_arr('ages'),      # ages
                space_arr(1)
            )
        ));
    }

    public function testUnclosedGroup() {
        $str = '(';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(array(
                'token' => 'group-enter',
                'match' => '(',
            ))
        ));
    }

    public function testEmptyGroup() {
        $str = '()';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(array(
                'token' => 'group-enter',
                'match' => '(',
                'exit' => ")"
            ))
        ));
    }

    public function testNestedGroups() {
        $str = '(()())';
        $tree = $this->parser->apply($str, 'root')->tokenize();
        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(array(
                'token' => 'group-enter',
                'match' => '(',
                'children' => array(
                    array(
                        'token' => 'group-enter',
                        'match' => '(',
                        'exit' => ")"
                    ),
                    array(
                        'token' => 'group-enter',
                        'match' => '(',
                        'exit' => ")"
                    )
                ),
                'exit' => ")"
            ))
        ));
    }

    public function testComplexGroupsAndWords() {
        $str = 'once( upon(a  )time   (there)    lived)a     moose';
        $tree = $this->parser->apply($str, 'root')->tokenize();

        check($tree, array(
            'token' => 'root',
            'match' => null,
            'children' => array(
                word_arr('once'),                       #   once
                array(
                    'token' => 'group-enter',
                    'match' => '(',                     #   (
                    'children' => array(
                        space_arr(1),
                        word_arr('upon'),               #       upon
                        array(
                            'token' => 'group-enter',
                            'match' => '(',             #       (
                            'children' => array(
                                word_arr('a'),          #           a
                                space_arr(2)
                            ),
                            'exit' => ')'               #       )
                        ),
                        word_arr('time'),               #       time
                        space_arr(3),
                        array(
                            'token' => 'group-enter',
                            'match' => '(',             #       (
                            'children' => array(
                                word_arr('there'),      #           there
                            ),
                            'exit' => ')'               #       )
                        ),
                        space_arr(4),
                        word_arr('lived'),              #       lived
                    ),
                    'exit' => ")"                       #   )
                ),
                word_arr('a'),                          #   a
                space_arr(5),
                word_arr('moose')                       #   moose
            )
        ));
    }

}

/**
 * Space token fixture
 */
function space_arr($num) {
    return array(
        'token' => 'space',
        'match' => str_repeat(' ', $num),
    );
}

/**
 * Word token fixture
 */
function word_arr($word) {
    return array(
        'token' => 'word',
        'match' => $word,
    );
}
