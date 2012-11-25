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
		));
	}

	public function testSpaceOnly() {
		$str = '    ';
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'children' => array(
				token_arr('space', '    ')
			)
		));
	}

	public function testSingleQuoteString() {
		$str = "'a'";
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'children' => array(
				token_arr('str-1-s', "'", 'a', "'")
			)
		));
	}

	public function testIdentifierOnly() {
		$str = "hello";
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'children' => array(
				token_arr('identifier', "hello")
			)
		));
	}

	public function testIdentifierAndValue() {
		$str = "hello: 50";
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'children' => array(
				token_arr('identifier', "hello"),
				token_arr('colon', ":"),
				token_arr('space', " "),
				token_arr('int', "50")
			)
		));
	}

	public function testNumbersAndIdentifier() {
		$str = "12 3.4 hello";
		$tree = Runtime::parse($str);
		check($tree, array(
			'token' => 'root',
			'match' => null,
			'children' => array(
				token_arr('int', "12"),
				token_arr('space', " "),
				token_arr('float', "3.4"),
				token_arr('space', " "),
				token_arr('identifier', "hello")
			)
		));
	}
}

/**
 * Token fixture
 */
function token_arr($token, $match, $literal = null, $exit = null) {
	$arr = array(
		'token' => $token,
		'match' => $match
	);
	if(!is_null($literal)) {
		$arr['children'] = array(
			array(
				'token' => '&literal',
				'match' => $literal
			)
		);
	}
	if(!is_null($exit)) {
		$arr['exit'] = $exit;
	}
	return $arr;
}