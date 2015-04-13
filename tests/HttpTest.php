<?php

require __DIR__ . '/../vendor/autoload.php';

/**
 * All tests for Http class
 * @author Nick Tucci <nicktucci@hotmail.ca>
 * @version 1.0
 * @package Damon
 * @copyright 2015 https://github.com/Tuccinator/Damon
 */
class HttpTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Test to attempt connection with Google
	 */
	public function testConnect()
	{
		$request = new Damon\Http('https://www.google.ca/?gfe_rd=cr&ei=sG33VMGnOOOy8wfvxIHgBgcom');

		$html = $request->connect()->getRawHtml();

		$this->assertContains('Google', $html);
	}

	/**
	 * Test to create a new resource successfully
	 */
	public function testCreateNew()
	{
		$request = new Damon\Http('https://www.google.ca/?gfe_rd=cr&ei=sG33VMGnOOOy8wfvxIHgBg');

		$html = $request->connect()->getHtml();

		$newWebsite = $request->createNew('http://example.com')->connect()->getHtml();

		$this->assertNotSame($html, $newWebsite);
		$this->assertContains('Example', $newWebsite);
	}
}