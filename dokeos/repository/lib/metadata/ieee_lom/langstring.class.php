<?php
/**
 * $Id$
 * @package repository.metadata
 * @subpackage ieee_lom
 */
/**
 * A LangString field used in IEEE LOM.
 * This object zero or more strings in different languages
 */
class LangString 
{
    const STRING               = 'string';
    const LANGUAGE             = 'language';
    
	/**
	 * Array containing all strings
	 */
	protected $strings;
	
	/**
	 * Constructor
	 * @param string|null $string The text
	 * @param string|null $language The language of the $string parameters
	 */
	public function LangString($string = null, $language = null)
	{
		$this->strings = array();
		
		if(isset($string))
		{
    		$this->add_string($string, $language);
		}
	}
	/**
	 * Adds a new string to the set of strings
	 * @param string|null $string The text
	 * @param string|null $language The language of the $string parameters
	 */
	public function add_string($string = null, $language = null)
	{
		$new_string[self :: STRING]   = $string;
		$new_string[self :: LANGUAGE] = $language;
		$this->strings[]              = $new_string;
	}
	/**
	 * Gets the strings
	 * @return array This array is of the form
	 * <pre>
	 *  [0]['language'] = 'XX';
	 *  [0]['string'] = 'XXXXXX';
	 *  [1]['language'] = ...;
	 *  ...
	 * </pre>
	 */
	public function get_strings()
	{
		return $this->strings;
	}
}
?>