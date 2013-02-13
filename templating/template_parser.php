<?php
/**
 * Simple, but very light weight template parser.
 *
 * Can't take credit for most of this class, but I forgot where 
 * the original tutorial / complete class came from.
 */
class Page
{
	// Used to store the page as it's being generated.
	var $page;
	
	/*.
	 * Constructor
	 * @template: the template to use.
	 */
	function Page($template = 'std.tpl') {
		// check to see if the template file exists
		// using @ due to an error in php 5
		// see http://bugs.php.net/bug.php?id=41518
		if (@file_exists($template))
			// load it into memory
			$this->page = join('', file($template));
		else
			die("Template file $template not found.");
	}

	/**
	 * Parse accepts the name of a file and includes its contents 
	 * (include will processes any PHP directives found within the file). 
	 * Output buffering is used to store the processed data so we can return it and prevents 
	 * the included file from being sent to standard output prematurely.
	 * Please parse nothing but php files.
	 */
	function parse($file) {
		$extention = substr($file, -3);
		if ($extention != "php"){
			$buffer = $file;
		}
		elseif (is_dir($file)){
			$buffer = $file;
		}
		else{
			//start buffering
			ob_start();
			include($file);
			$buffer = ob_get_contents();
			ob_end_clean();
		}
		return $buffer;
	}

	/**
	 * Function to replace template tags with actual data.
	 * @tags: array with tags to replace.
	 */
	function replace_tags($tags = array()) {
		if (sizeof($tags) > 0){
			foreach ($tags as $tag => $data) {
				// see if the value is a filename . If it is then the file's contents is loaded as the replacement data; if it isn't then it's assumed the data was passed as straight text.
				$data = (@file_exists($data)) ? $this->parse($data) : $data;
				$data = "" . $data;
				$this->page = eregi_replace('{' . $tag . '}', $data, $this->page);
			}
		}
		else
			die('No tags designated for replacement.');
	}

	/**
	 * Display the page.
	 */
	function output() {
		print($this->page);
	}
	
	/**
	 * Return the page as a variable.
	 */
	function getPage() {
		return $this->page;
	}
}
?>