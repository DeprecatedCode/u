<?php

/**
 * U Engine test
 */
use NateFerrero\u\Runtime;

class EngineTest {

	public function integerTest() {
		$result = Runtime::exec(
			'a: 12 + 34 + 10, b: -10, c: +321, d: 89 + 11 / 10,
			 e: 100 / -10, f: b / e * d');
		check($result->a, 56);
		check($result->b, -10);
		check($result->c, 321);
		check($result->d, 10);
		check($result->e, -10);
		check($result->f, 10);
	}

	public function booleanTest() {
		$result = Runtime::exec(
			'a: true && true, b: true && false, c: false && true, d: false && false
			 e: true || true, f: true || false, g: false || true, h: false || false
			 i: !true, j: !false, k: true && !false, l: false || !true
			 m: false || false || true, n: true && true && false
			 o: false || true && false, p: true && false || true');
		check($result->a, true);
		check($result->b, false);
		check($result->c, false);
		check($result->d, false);
		check($result->e, true);
		check($result->f, true);
		check($result->g, true);
		check($result->h, false);
		check($result->i, false);
		check($result->j, true);
		check($result->k, true);
		check($result->l, false);
		check($result->m, true);
		check($result->n, false);
		check($result->o, false);
		check($result->p, true);
	}

	public function comparatorTest() {
		$result = Runtime::exec(
			'a: 12 > -15.4, b: -5 >= -6');
		check($result->a, true);
		check($result->b, true);
	}

}