<?php

/**
 * Sparse test
 */
use NateFerrero\u\Sparse;
use NateFerrero\u\SparseDocument;

class SparseTest {

    public function testTreeGeneration() {
        $parser = new Sparse(array('root' => array(), '&tokens' => array()));
        $doc = $parser->apply('', 'root');
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

    public function fail() {
        check(false, true);
    }

}
