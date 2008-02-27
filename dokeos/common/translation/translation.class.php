<?php
class Translation
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;
	
	/**
	 * Language strings defined in the language-files. Stored as an associative array.
	 */
	private $strings;
	
	/**
	 * The language we're currently translating too
	 */
	private $language;
	
	/**
	 * The application we're currently translating
	 */
	private $application;

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
		$this->strings = array();
	}
	
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			self :: $instance = new self();
		}
		return self :: $instance;
	}

	/**
	 * Returns the instance of this class.
	 * @return Translation The instance.
	 */
	static function get_lang($variable)
	{
		$instance = self :: get_instance();
		return $instance->translate($variable);
	}
	
	static function get_language()
	{
		$instance = self :: get_instance();
		return $instance->language;
	}
	
	static function set_language($language)
	{
		$instance = self :: get_instance();
		$instance->language = $language;
	}
	
	static function get_application()
	{
		$instance = self :: get_instance();
		return $instance->application;
	}	
	
	static function set_application($application)
	{
		$instance = self :: get_instance();
		$instance->application = $application;
	}

	/**
	 * Gets a parameter from the configuration.
	 * @param string $section The name of the section in which the parameter
	 *                        is located.
	 * @param string $name The parameter name.
	 * @return mixed The parameter value.
	 */
	function translate($variable)
	{
		$language = $this->language;
		
		if (!is_array($this->strings[$language]['general']))
		{
			$this->add_language_file_to_array($language, 'general');
		}
		
		$application = $this->get_application();
				
		if (!is_array($this->strings[$language][$application]))
		{
			$this->add_language_file_to_array($language, $application);
		}
		
		$strings = $this->strings;		
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
			return '[='. $application . '=' . $variable .'=]';
		}
	}
	
	function add_language_file_to_array($language, $application)
	{
		$lang = array();
		$path = Path :: get_path(SYS_LANG_PATH) . $language . '/' . $application . '.inc.php';
		include_once($path);
		$this->strings[$language][$application] = $lang[$application];
	}
}
?>