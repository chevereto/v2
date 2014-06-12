<?php
/* --------------------------------------------------------------------

  Chevereto
  http://chevereto.com/

  @version	2.6.0
  @author	Rodolfo Berríos A. <http://rodolfoberrios.com/>
			<inbox@rodolfoberrios.com>

  Copyright (c) Rodolfo Berrios <inbox@rodolfoberrios.com>
  
  Licensed under the MIT license
  http://opensource.org/licenses/MIT

  --------------------------------------------------------------------- */
  
/**
 * class minify
 * This class minify a js/css file:
 * 		- Read a file and saves the minify version to a given target
 *		- Default target is souce.min.ext
 * 		- Works only with JS and CSS files
 *
 * @author		Rodolfo Berríos <http://rodolfoberrios.com/>
 * @url			<http://chevereto.com>
 * @package		Chevereto
 *
 * @copyright	Rodolfo Berríos <inbox@rodolfoberrios.com>
 *
 */

class Minify {
	
	function __construct($source='')
	{
		if($source!=='') {
			$this->addSource($source);
		}
	}
	
	/**
	 * addSource
	 * Adds the filepath to be minified
	 * 
	 * @param	string
	 */
	public function addSource($source) {
		$this->source = $source;
		if(!$this->can_read_source()) {
			throw new MinifyException('Source file "'.$source.'" can\'t be loaded. Make sure that the file exists on the given path.');
		}
		if(!$this->is_valid_source()) {
			throw new MinifyException('Invalid type. This only works with JS and CSS files.');
		}
		$this->type = preg_replace('/^.*\.(css|js)$/i', '$1', $source);
		$this->data = $this->get_source_data();
	}
	
	/**
	 * setTarget
	 * Sets where you want to save the minified file
	 * 
	 * @param	string
	 */
	public function setTarget($target='') {
		$this->target = ($target == '' ? $this->get_default_target() : $target);
	}
	
	/**
	 * exec
	 * Does the minification process
	 */
	public function exec() {
		if(!isset($this->source)) {
			throw new MinifyException('There is no source defined. Use the class constructor or ->addSource(\'/source-dir/source.file\') to add a source to be minified.');
		}
		switch($this->type) {
			case 'css':
				$this->minifyCSS();
			break;
			case 'js':
				$this->minifyJS();
			break;
		}
		$this->save_to_file();
	}
	
	/**
	 * get_source_data
	 * Grabs the file data using file_get_contents
	 *
	 * @return: string
	 */
	private function get_source_data() {
		$data = @file_get_contents($this->source);
		if($data === false) {
			throw new MinifyException('Can\'t read the contents of the source file.');
		} else {
			return $data;
		}
	}
	
	/**
	 * can_read_source
	 * Tells if the source file can be readed or not
	 *
	 * @return: bool
	 */
	private function can_read_source() {
		return (@file_exists($this->source) && is_file($this->source) ? true : false);
	}
	
	/**
	 * is_valid_source
	 * Tells if the source is valid by its extension
	 *
	 * @return: bool
	 */
	private function is_valid_source() {
		return preg_match('/^.*\.(css|js)$/i', $this->source);
	}
	
	/**
	 * minifyCSS
	 * Sets the minified data for CSS
	 */
	private function minifyCSS() {
		$pre = (preg_match('/(\/\*.+@Chevereto:\s+[0-9\.]{1,}\s+\*\/)/is', $this->data, $matches) ? $matches[0]."\n" : '');
		$this->set_minified_data($pre.$this->get_minified_data());
	}
	
	/**
	 * minifyJS
	 * Sets the minified data for JavaScript
	 */
	private function minifyJS() {
		if(preg_match('/BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA/i', $this->data)) {
			$pre = '/* Chevereto @'.get_chevereto_version().' | Copyright (C) '.date('Y').' Rodolfo Berríos A. All rights reserved | chevereto.com/license */'."\n";
		} else {
			$pre = '';
		}
		$this->set_minified_data($pre.$this->get_minified_data($this->data));
	}
	
	/**
	 * get_default_target
	 * Set the default file.min.ext from $this->source 
	 *
	 * @return: string
	 */
	private function get_default_target() {
		return preg_replace('/(.*)\.([a-z]{2,3})$/i', '$1.min.$2', $this->source);
	}
	
	/**
	 * get_minified_data
	 * Returns the minified $this->data
	 *
	 * @return: string
	 */
	private function get_minified_data() {
		return $this->strip_whitespaces($this->strip_linebreaks($this->strip_comments($this->data)));
	}
	
	/**
	 * set_minified_data
	 * Sets $this->minified_data and unset the $this->data
	 */
	private function set_minified_data($string) {
		$this->minified_data = $string;
		unset($this->data);
	}
	
	/**
	 * save_to_file
	 * Saves the minified data to the target file
	 */
	private function save_to_file()
	{
		$this->target = (!isset($this->target) ? $this->get_default_target() : $this->target);
		if(!isset($this->minified_data)) {
			throw new MinifyException('There is no data to write to "'.$this->target.'"');
		}
		if(($handler = @fopen($this->target, 'w')) === false) {
			throw new MinifyException('Can\'t open "' . $this->target . '" for writing.');
		}
		if(@fwrite($handler, $this->minified_data) === false) {
			throw new MinifyException('The file "' . $path . '" could not be written to. Check if PHP has enough permissions.');
		}
		@fclose($handler);
	}
	
	/**
	 * strip_whitespaces
	 * Removes any whitespace inside/betwen ;:{}[] chars. It also safelky removes the extra space inside () parentheses
	 *
	 * @param: string
	 */
	private function strip_whitespaces($string) {
		switch($this->type) {
			case 'css':
				$pattern = ';|:|,|\{|\}';
			break;
			case 'js':
				$pattern = ';|:|,|\{|\}|\[|\]'; 
			break;
		}
		return preg_replace('/\s*('.$pattern.')\s*/', '$1', preg_replace('/\(\s*(.*)\s*\)/', '($1)', $string));
	}
	
	/**
	 * strip_linebreaks
	 * Removes any line break in the form of newline, carriage return, tab and extra spaces
	 *
	 * @param: string
	 */
	private function strip_linebreaks($string) {
		return preg_replace('/(\\\?[\n\r\t]+|\s{2,})/', '', $string);
	}
	
	private function strip_comments($string) {
		// Don't touch anything inside a quote or regex
		$protected = '(?<![\\\/\'":])';
		// Comment types
		$multiline = '\/\*[^*]*\*+([^\/][^\*]*\*+)*\/'; // /* comment */
		$html = '<!--([\w\s]*?)-->'; // <!-- comment -->
		$ctype = '\/\/.*'; // //comment (Yo Dawg)!
		// The pattern
		$pattern = $protected;
		switch($this->type) {
			case 'css':
				$pattern .= $multiline;
			break;
			case 'js':
				$pattern .= '('.$multiline.'|'.$html.'|'.$ctype.')'; 
			break;
		}
		return preg_replace('#'.$pattern.'#', '', $string);
	}

}

class MinifyException extends Exception {}