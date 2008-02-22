<?php
class Translation
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;
	
	/**
	 * The language files to be used for translation
	 */
	private $language_files;
	
	/**
	 * Language strings defined in the language-files. Stored as an associative array.
	 */
	private $strings;
	
	/**
	 * The language we're currently translating too
	 */
	private $language;

	/**
	 * Constructor.
	 */
	private function Translation($language = null)
	{
		if (is_null($language))
		{
			global $language_interface;
			$this->language = $language_interface;
		}
		else
		{
			$this->language = $language;
		}
	}

	/**
	 * Returns the instance of this class.
	 * @return Translation The instance.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			self :: $instance = new self();
		}
		return self :: $instance;
	}
	
	function use_lang_files()
	{
		foreach (func_get_args() as $id)
		{
			if (is_array($id))
			{
				foreach ($id as $i)
				{
					$this->language_files[$i] = true;
				}
			}
			else
			{
				$this->language_files[$id] = true;
			}
		}
	}
	
	function add_language_file_to_array($language, $application)
	{
		$lang = array();
		$path = Path :: get_path(SYS_CODE_PATH) . 'lang/' . $language . '/' . $application . '.inc.php';
		include_once($path);
		$this->strings[$language][$application] = $lang[$application];
	}
	
	function ignore_lang_files()
	{
		foreach (func_get_args() as $id)
		{
			if (is_array($id))
			{
				foreach ($id as $i)
				{
					unset($this->language_files[$i]);
				}
			}
			else
			{
				unset($this->language_files[$id]);
			}
		}
	}

	/**
	 * Gets a parameter from the configuration.
	 * @param string $section The name of the section in which the parameter
	 *                        is located.
	 * @param string $name The parameter name.
	 * @return mixed The parameter value.
	 */
	function get_translation($variable, $application)
	{
		$language = $this->language;
		
		if (!is_array($this->strings[$language]['general']))
		{
			$this->add_language_file_to_array($language, 'general');
		}
		
		$language_files = $this->language_files;
		
		foreach ($language_files as $file => $value)
		{
			if (!is_array($this->strings[$language][$file]))
			{
				$this->add_language_file_to_array($language, $file);
			}
		}
		
		$strings = $this->strings;
		
		//echo '<pre>';
		//print_r($lang);
		//echo '<pre>';
		
		if (isset($strings[$language][$application][$variable]))
		{
			return $strings[$language][$application][$variable];
		}
		elseif (isset($strings[$language]['general'][$variable]))
		{
			return $strings[$language]['general'][$variable];
		}
		else
		{
			return '[='. $application . '::' . $variable .'=]';
		}
	}
}
?>