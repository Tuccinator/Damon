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
	 * Number of the times the recursive function to find parent has iterated
	 * @var int
	 */
	private $_recursiveCount = 0;

	/**
	 * Set the exceptions array
	 */
	public function __construct()
	{
		$this->_exceptions = [
			'input',
			'hr',
			'br',
			'meta',
			'link'
		];
	}

	/**
	 * Parse the extracted HTML and transform into individual tokens
	 * @var string $html The HTML source extracted from a remote website
	 * @return array Array of all extracted tokens
	 */
	public function parse($html = '<div class="bob" id="id2"><p id="firstParagraph"></p></div>')
	{
		$firstSequence = explode($this->_startDelimiter, $html);
		$secondSequence = $this->_secondSequence($firstSequence);
		$this->_tokens = $this->_getElements($secondSequence);

		$this->_tokens = $this->_sortElements();

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
	private function _sortElements()
	{
		$newElements = [];
		
		$this->_recursiveParent(0);

		return $this->_tokens;
	}

	/**
	 * Recursive function for iterating through each element to sort
	 * @var integer $elementId	Key of element in tokens array
	 * @var array 	$openTags	All of the open tags that have yet to be closed i.e. <div>....</div>
	 * @return array The new tokens array
	 */
	private function _recursiveParent($elementId, $openTags = array())
	{
		// When end of token array is reach, quit and return tokens
		if($elementId == count($this->_tokens) - 1):
			return $this->_tokens;
		endif;

		// set current element
		$element = $this->_tokens[$elementId];
		
		// if there are open tags, proceed
		if(count($openTags) > 0):

			// check that last open tag is same as current element
			if($element['tag'] === '/' . $openTags[0]['tag']):

				// Remove the last open tag from all other tags
				$lastTag = array_shift($openTags);

				if(count($openTags) > 0):
					// Set the parent to the ID of last open tag
					$this->_tokens[$lastTag['id']]['parent'] = $this->_tokens[$openTags[0]['id']]['id'];
				endif;
			else:

				// When the tag is an inline element, automatically set the parent to last open tag
				if(in_array($element['tag'], $this->_exceptions)) {
					$this->_tokens[$elementId]['parent'] = $this->_tokens[$openTags[0]['id']]['id'];
				}

				// If the current element isn't inline and it is not a closing tag, add to beginning of open tag array
				if(strstr($element['tag'], '/') == false && !in_array($element['tag'], $this->_exceptions)) {
					array_unshift($openTags, ['tag' => $element['tag'], 'id' => $elementId]);
				}
			endif;
		else:
			// add the initial open tag to array
			$openTags[] = ['tag' => $element['tag'], 'id' => $elementId];
		endif;

		// move onto the next element
		$this->_recursiveParent($elementId + 1, $openTags);
	}

	/**
	 * Compile the completed token array into a new HTML file
	 * INCOMPLETE
	 */
	public function compileTokens(){}

	/**
	 * Retrieve the inner text in between tags
	 * INCOMPLETE
	 */
	public function getInnerText(){}

	/**
	 * Set the new inner text for a certain tag. Must compile tokens to see any changes
	 * @var string $tag Tag to be used for setting new text. i.e. '#name.first'
	 * INCOMPLETE
	 */
	public function setInnerText($tag){}

	/**
	 * Get the parent of said element
	 * @var string  $tag 		Tag
	 * @var array 	$attributes Attributes to search with tag
	 * @return array Whole parent with attributes
	 */
	public function getParent($tag, $attributes = null)
	{
		$parents = [];

		foreach($this->_tokens as $token) {
			if($token['tag'] == $tag) {
				$error = false;
				if(!is_null($attributes)) {
					foreach($attributes as $attribute => $value) {
						if($token['attributes'][$attribute] != $value) {
							$error = true;
						}
					}
				}
				if(!$error) {
					foreach($this->_tokens as $second) {
						if($second['id'] == $token['parent']) {
							array_push($parents, $second);
						}
					}
				}
			}
		}

		if(count($parents) == 1) {
			return $parents[0];
		}
		return $parents;
	}

	/**
	 * Get all children of an element
	 * @var string Tag
	 * @return array Array of all children
	 * INCOMPLETE
	 */
	public function getChildren($tag, $attributes = null)
	{
		$children = [];

		foreach($this->_tokens as $token) {
			$error = false;

			if($token['tag'] == $tag) {
				if(!is_null($attributes)) {
					foreach($attributes as $attribute => $value) {
						if($token['attributes'][$attribute] != $value) {
							$error = true;
						}
					}
				}

				if(!$error) {
					foreach($this->_tokens as $secondToken) {
						if(isset($secondToken['parent'])) {
							if($secondToken['parent'] == $token['id']) {
								array_push($children, $secondToken);
							}
						}
					}
				}
			}
		}

		if(count($children) == 1) {
			return $children[0];
		}
		return $children;
	}
} 