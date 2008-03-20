<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 survey_question_option
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyQuestionOption
{
	/**
	 * Dokeos185SurveyQuestionOption properties
	 */
	const PROPERTY_QUESTION_OPTION_ID = 'question_option_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_OPTION_TEXT = 'option_text';
	const PROPERTY_SORT = 'sort';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SurveyQuestionOption object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SurveyQuestionOption($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (SELF :: PROPERTY_QUESTION_OPTION_ID, SELF :: PROPERTY_QUESTION_ID, SELF :: PROPERTY_SURVEY_ID, SELF :: PROPERTY_OPTION_TEXT, SELF :: PROPERTY_SORT);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the question_option_id of this Dokeos185SurveyQuestionOption.
	 * @return the question_option_id.
	 */
	function get_question_option_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_OPTION_ID);
	}

	/**
	 * Returns the question_id of this Dokeos185SurveyQuestionOption.
	 * @return the question_id.
	 */
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}

	/**
	 * Returns the survey_id of this Dokeos185SurveyQuestionOption.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Returns the option_text of this Dokeos185SurveyQuestionOption.
	 * @return the option_text.
	 */
	function get_option_text()
	{
		return $this->get_default_property(self :: PROPERTY_OPTION_TEXT);
	}

	/**
	 * Returns the sort of this Dokeos185SurveyQuestionOption.
	 * @return the sort.
	 */
	function get_sort()
	{
		return $this->get_default_property(self :: PROPERTY_SORT);
	}

	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];

		if($array['del_files'] =! 1)
			$tool_name = 'survey_question_option';
		
		$coursedb = $array['course'];
		$tablename = 'survey_question_option';
		$classname = 'Dokeos185SurveyQuestionOption';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
}

?>