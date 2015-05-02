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

	/**
	 * Ensure that the getParent method returns the correct parent
	 */
	public function testGetParent()
	{
		$lexer = new \Damon\Lexer();
		$parents = '
			<div id="firstParent">
				<p id="paragraph1">Hi</p>
			</div>
			<div id="secondParent">
				<p id="paragraph2">Bye</p>
			</div>';
		
		$lexer->parse($parents);

		$this->assertEquals($lexer->getParent('p', ['id' => 'paragraph1'])['attributes']['id'], 'firstParent');
	}

	/**
	 * Ensure getChildren returns all children of related
	 */
	public function testGetChildren()
	{
		$lexer = new \Damon\Lexer();

		$elements = '
			<div id="firstParent">
				<p id="paragraph1">Hi</p>
			</div>
			<div id="secondParent">
				<p id="paragraph2">Bye</p>
			</div>';

		$lexer->parse($elements);

		$this->assertEquals($lexer->getChildren('div', ['id' => 'firstParent'])['attributes']['id'], 'paragraph1');
	}

	public function testGetElement()
	{
		$lexer = new \Damon\Lexer();
		$elements = '
			<div id="firstParent">
				<p id="paragraph1">Hi</p>
			</div>
			<div id="secondParent">
				<p id="paragraph2">Bye</p>
			</div>';

		$lexer->parse($elements);

		$this->assertEquals($lexer->getElement('p', ['id' => 'paragraph1'])['tag'], 'p');
	}

	public function testGetInnerText()
	{
		$lexer = new \Damon\Lexer();
		$elements = '
			<div id="firstParent">
				<p id="paragraph1">Hi</p>
			</div>
			<div id="secondParent">
				<p id="paragraph2">Bye</p>
			</div>';
		$lexer->parse($elements);

		$this->assertEquals($lexer->getInnerText('p', ['id' => 'paragraph2']), 'Bye');
	}
}

?>