<?php
require_once dirname(__FILE__).'/../../metadata/ieee_lom/langstring.class.php';

class LangStringMapper extends LangString
{
    const STRING_METADATA_ID   = 'string_metadata_id';
    const STRING_OVERRIDE_ID   = 'string_override_id';
    const STRING_ORIGINAL_ID   = 'string_original_id';
    const LANGUAGE_METADATA_ID = 'language_metadata_id';
    const LANGUAGE_OVERRIDE_ID = 'language_override_id';
    const LANGUAGE_ORIGINAL_ID = 'language_original_id';
    
    public function LangStringMapper($string = null, $language = null, $string_metadata_id = null, $language_metadata_id = null, $string_override_id = null, $language_override_id = null, $string_original_id = null, $language_original_id = null)
    {
        parent :: LangString($string, $language);
        
        if(isset($this->strings[0]))
        {
            $this->strings[0][self :: STRING_METADATA_ID]   = $string_metadata_id;
            $this->strings[0][self :: LANGUAGE_METADATA_ID] = $language_metadata_id;
            $this->strings[0][self :: STRING_OVERRIDE_ID]   = $string_override_id;
            $this->strings[0][self :: LANGUAGE_OVERRIDE_ID] = $language_override_id;
            $this->strings[0][self :: STRING_ORIGINAL_ID]   = $string_original_id;
            $this->strings[0][self :: LANGUAGE_ORIGINAL_ID] = $language_original_id;
            
            //$string_original_id = null, $language_original_id
        }
    }
    
	/**
	 * Adds a new string to the set of strings with the corresponding metadata ids
	 * @param string|null $string The text
	 * @param string|null $language The language of the $string parameters
	 */
	public function add_string($string = null, $language = null, $string_metadata_id = null, $language_metadata_id = null, $string_override_id = null, $language_override_id = null, $string_original_id = null, $language_original_id = null)
	{
	    $new_string                               = array();
		$new_string[parent :: STRING]             = $string;
		$new_string[parent :: LANGUAGE]           = $language;
		
		$new_string[self :: STRING_METADATA_ID]   = $string_metadata_id;
		$new_string[self :: LANGUAGE_METADATA_ID] = $language_metadata_id;
		
		$new_string[self :: STRING_OVERRIDE_ID]   = $string_override_id;
        $new_string[self :: LANGUAGE_OVERRIDE_ID] = $language_override_id;
        
        $new_string[self :: STRING_ORIGINAL_ID]   = $string_original_id;
        $new_string[self :: LANGUAGE_ORIGINAL_ID] = $language_original_id;
            
		$this->strings[]                          = $new_string;
	}
}
?>