<?php

/**
 * U Grammar test
 */
use NateFerrero\u\Runtime;

class GrammarTest {

	public function testEmpty() {
		$str = '';
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'content' => ''
		));
	}

	public function testSpaceOnly() {
		$str = '    ';
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'content' => '',
			'children' => array(
				token_arr('space', '    ')
			)
		));
	}

	public function testSingleQuoteString() {
		$str = "'a''\\'";
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'content' => '',
			'children' => array(
				token_arr('str-1-s', "'", 'a', "'")
				array(
					'token' => 'str-1-s',
					'match' => "'",
					'content' => 'a', "'")
			)
		));
	}
}

/**
 * Token fixture
 */
function token_arr($token, $match, $content = '', $exit = null) {
	$arr = array(
		'token' => $token,
		'match' => $match,
		'content' => $content
	);
	if(!is_null($exit)) {
		$arr['exit'] = $exit;
	}
	return $arr;
}