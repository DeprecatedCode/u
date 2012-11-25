<?php

/**
 * U Engine test
 */
use NateFerrero\u\Runtime;

class EngineTest {

	public function integerTest() {
		$result = Runtime::exec('a: 12 + 34');
		check($result->a, 56);
	}

}