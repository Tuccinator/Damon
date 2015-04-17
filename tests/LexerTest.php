<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * All tests for Lexer class
 * @author Nick Tucci <nicktucci@hotmail.ca>
 * @version 1.0
 * @package Damon
 * @copyright 2015 https://github.com/Tuccinator/Damon
 */
class LexerTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Ensure that it effectively sorts the attributes
	 */
	public function testParse()
	{
		$lexer = new \Damon\Lexer();
		$tokens = $lexer->parse('<div><span id="price">500</span></div>');

		$this->assertEquals('price', $tokens[1]['attributes']['id']);
	}

	/**
	 * Ensure the sort mechanism detects the correct parent
	 */
	public function testParent()
	{
		$lexer = new \Damon\Lexer();
		$tokens = $lexer->parse('<div><span id="price">500</span></div>');

		$this->assertEquals($tokens[1]['parent'], $tokens[0]['id']);
	}
}

?>