<?php
namespace Damon;

/**
 * A class to handle all HTTP requests and responses for lexer
 * @author Nick Tucci <nicktucci@hotmail.ca>
 * @version 1.0
 * @package Damon
 * @copyright 2015 https://github.com/Tuccinator/Damon
 */
class Http
{
	/**
	 * cURL handle
	 * @var Object
	 */
	private $_handle;

	/**
	 * cURL options
	 * @var array
	 */
	private $_options = array();

	/**
	 * HTML source of the page being requested
	 * @var string
	 */
	private $_source;

	/**
	 * Create a new cURL handle
	 * @param string $url URL to use for request
	 */
	public function __construct($url = 'http://example.com')
	{
		// Create a new cURL resource
		$this->createNew($url);
	}

	/**
	 * Connect to the remote site using handle and options
	 * @return Object Self
	 */
	public function connect()
	{
		// Use options for cURL request
		curl_setopt_array($this->_handle, $this->_options);

		// Execute cURL request
		$html = curl_exec($this->_handle);

		// Close the cURL request
		curl_close($this->_handle);

		// Save the HTML data
		$this->_source = $html;

		return $this;
	}

	/**
	 * Use own array of options for cURL request
	 * @param array $options Options for cURL
	 */
	public function setOptions($options = array())
	{
		// Ensure that data is an options array or else throw an exception
		if(!is_array($options)) {
			throw new \Exception(__METHOD__ . ': $options must be an array, ' . gettype($options) . ' given. Use setOption() for single options.');
		}

		// Use default options if no data is passed in method
		if(empty($options)) {
			$this->_options = [
				CURLOPT_URL => 'http://example.com',
				CURLOPT_RETURNTRANSFER => true,
			];
		} else {
			$this->_options = $options;
		}
	}

	/**
	 * Add a custom option to cURL handle
	 * @param constant 	$option cURL setopt option
	 * @param string 	$value  Value of the option
	 */
	public function setOption($option, $value)
	{
		$this->_options[$option] = $value;
	}

	/**
	 * Retrieve the array of options
	 * @return array cURL options
	 */
	public function getOptions()
	{
		return $this->_options;
	}

	/**
	 * Retrieve the raw HTML data, i.e. automatically outputs HTML
	 * @return string Raw HTML source of request
	 */
	public function getRawHtml()
	{
		return $this->_source;
	}

	/**
	 * Retrieve the HTML data after being sanitized
	 * @return string HTML source of request
	 */
	public function getHtml()
	{
		// Sanitize HTML data so it won't automatically output onto screen
		return htmlentities($this->_source);
	}

	/**
	 * Change the current URL to a new one
	 * @param string $url URL for request
	 */
	public function setUrl($url)
	{
		$this->_options[CURLOPT_URL] = $url;

		return $this;
	}

	/**
	 * Create a whole cURL resource
	 * @param  string $url URL for request
	 * @return Object      self
	 */
	public function createNew($url = 'http://example.com')
	{
		// Create a new cURL handle
		$this->_handle = curl_init();

		// Create the options for cURL
		$this->setOptions();

		// Set the URL
		$this->setUrl($url);

		return $this;
	}
}