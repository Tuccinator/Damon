<?php
namespace Damon;

/**
 * A class to handle lexical analysis and token management
 * @author Nick Tucci <nicktucci@hotmail.ca>
 * @version 1.0
 * @package Damon
 * @copyright 2015 https://github.com/Tuccinator/Damon
 */
class Lexer
{
	/**
	 * Beginning delimiter for lexer
	 * @var string
	 */
	private $_startDelimiter = '<';

	/**
	 * Ending delimiter for lexer
	 * @var string
	 */
	private $_endDelimiter = '>';

	/**
	 * All tokens extracted from HTML source
	 * @var array	
	 */
	private $_tokens;

	/**
	 * An array of HTML elements that close themselves
	 * @var array
	 */
	private $_exceptions;

	/**
	 * Set the exceptions array
	 */
	public function __construct()
	{
		$this->_exceptions = [
			'input',
			'hr',
			'br'
		];
	}

	/**
	 * Parse the extracted HTML and transform into individual tokens
	 * @var string $html The HTML source extracted from a remote website
	 * @return array Array of all extracted tokens
	 */
	public function parse($html = '<div class="bob" id="id2"><p id="firstParagraph"></div>')
	{
		$firstSequence = explode($this->_startDelimiter, $html);
		$secondSequence = $this->_secondSequence($firstSequence);
		$elements = $this->_getElements($secondSequence);

		$this->_tokens = $elements;

		return $this->_tokens;
	}

	/**
	 * Second sequence to remove the ending right-chevron from elements
	 * @var array $firstTokens First sequence of tokens with removed beginning-chevron from element
	 * @return array Completed array of tokens
	 */
	private function _secondSequence($firstTokens)
	{
		$tokens = [];

		foreach($firstTokens as $token)
		{
			$tokens[] = explode('>', $token);
		}

		array_shift($tokens);

		return $tokens;
	}

	/**
	 * Associate the tokens with their proper element type
	 * @var array $tags The element strings retrieved from the sequence process i.e. div class="bob" id="name"
	 * @return array New array of tokens with all elements sorted properly
	 */
	private function _getElements($tags)
	{
		$elements = [];
		
		foreach($tags as $tag)
		{
			$tagValue = $tag[0];
			if(!strstr($tagValue, ' ')) {
				$elements[] = [
					'id' => uniqid(),
					'tag' => $tagValue
				];
				continue;
			}
			$elementExploded = explode(' ', $tagValue);
			if(isset($elementExploded[1])) {
				$element = $elementExploded[0];
				array_shift($elementExploded);
				$attributeString = implode('=', $elementExploded);
				$attributes = $this->_getAttributes($attributeString);

				$elements[] = [
					'id' => uniqid(),
					'tag' => $element,
					'attributes' => $attributes
				];
			}
		}

		return $elements;
	}

	/**
	 * Fetch the attributes from an "attribute string"
	 * @var string $tag An attribute string from an HTML element. i.e. class="bob" id="name"
	 */
	private function _getAttributes($tag)
	{
		$attributes = [];

		$pairs = explode('=', $tag);

		for($i = 0; $i < count($pairs); $i += 2)
		{
			$attributeName = trim($pairs[$i]);
			$attributeValue = @trim($pairs[$i + 1], '"');

			$attributes[$attributeName] = $attributeValue;
		}

		return $attributes;
	}

	/**
	 * Remove a specific element from the HTML source
	 * @var string $tag HTML tag string i.e. #name.bob
	 */
	public function removeElement($tag)
	{
		/**
		 * INCOMPLETE, WILL BE ADDED IN v1.1
		 */
		foreach($this->_tokens as $key => $token)
		{
			if($token['tag'] == $tag) {
				unset($this->_tokens[$key]);
			}
		}

		return $this->_tokens;
	}

	/**
	 * Sort through all of the tokens and amalgamate parent/children
	 * @var array $elements All hitherto formatted tokens
	 * @return array Newly formatted list of tokens with parents/children
	 */
	private function _sortElements($elements)
	{
		$newElements = [];

		return $newElements;
	}

	/**
	 * Compile the completed token array into a new HTML file
	 * INCOMPLETE
	 */
	public function compileTokens(){}

} 