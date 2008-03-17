<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 survey_answer
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyAnswer
{
	/**
	 * Dokeos185SurveyAnswer properties
	 */
	const PROPERTY_ANSWER_ID = 'answer_id';
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_OPTION_ID = 'option_id';
	const PROPERTY_VALUE = 'value';
	const PROPERTY_USER = 'user';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SurveyAnswer object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SurveyAnswer($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ANSWER_ID, SELF :: PROPERTY_SURVEY_ID, SELF :: PROPERTY_QUESTION_ID, SELF :: PROPERTY_OPTION_ID, SELF :: PROPERTY_VALUE, SELF :: PROPERTY_USER);
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
	 * Returns the answer_id of this Dokeos185SurveyAnswer.
	 * @return the answer_id.
	 */
	function get_answer_id()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWER_ID);
	}

	/**
	 * Sets the answer_id of this Dokeos185SurveyAnswer.
	 * @param answer_id
	 */
	function set_answer_id($answer_id)
	{
		$this->set_default_property(self :: PROPERTY_ANSWER_ID, $answer_id);
	}
	/**
	 * Returns the survey_id of this Dokeos185SurveyAnswer.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Sets the survey_id of this Dokeos185SurveyAnswer.
	 * @param survey_id
	 */
	function set_survey_id($survey_id)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_ID, $survey_id);
	}
	/**
	 * Returns the question_id of this Dokeos185SurveyAnswer.
	 * @return the question_id.
	 */
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}

	/**
	 * Sets the question_id of this Dokeos185SurveyAnswer.
	 * @param question_id
	 */
	function set_question_id($question_id)
	{
		$this->set_default_property(self :: PROPERTY_QUESTION_ID, $question_id);
	}
	/**
	 * Returns the option_id of this Dokeos185SurveyAnswer.
	 * @return the option_id.
	 */
	function get_option_id()
	{
		return $this->get_default_property(self :: PROPERTY_OPTION_ID);
	}

	/**
	 * Sets the option_id of this Dokeos185SurveyAnswer.
	 * @param option_id
	 */
	function set_option_id($option_id)
	{
		$this->set_default_property(self :: PROPERTY_OPTION_ID, $option_id);
	}
	/**
	 * Returns the value of this Dokeos185SurveyAnswer.
	 * @return the value.
	 */
	function get_value()
	{
		return $this->get_default_property(self :: PROPERTY_VALUE);
	}

	/**
	 * Sets the value of this Dokeos185SurveyAnswer.
	 * @param value
	 */
	function set_value($value)
	{
		$this->set_default_property(self :: PROPERTY_VALUE, $value);
	}
	/**
	 * Returns the user of this Dokeos185SurveyAnswer.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Sets the user of this Dokeos185SurveyAnswer.
	 * @param user
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}

}

?>