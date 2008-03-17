<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 shared_survey_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SharedSurveyQuestion
{
	/**
	 * Dokeos185SharedSurveyQuestion properties
	 */
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_SURVEY_QUESTION = 'survey_question';
	const PROPERTY_SURVEY_QUESTION_COMMENT = 'survey_question_comment';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_DISPLAY = 'display';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_CODE = 'code';
	const PROPERTY_MAX_VALUE = 'max_value';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SharedSurveyQuestion object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SharedSurveyQuestion($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_QUESTION_ID, SELF :: PROPERTY_SURVEY_ID, SELF :: PROPERTY_SURVEY_QUESTION, SELF :: PROPERTY_SURVEY_QUESTION_COMMENT, SELF :: PROPERTY_TYPE, SELF :: PROPERTY_DISPLAY, SELF :: PROPERTY_SORT, SELF :: PROPERTY_CODE, SELF :: PROPERTY_MAX_VALUE);
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
	 * Returns the question_id of this Dokeos185SharedSurveyQuestion.
	 * @return the question_id.
	 */
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}

	/**
	 * Sets the question_id of this Dokeos185SharedSurveyQuestion.
	 * @param question_id
	 */
	function set_question_id($question_id)
	{
		$this->set_default_property(self :: PROPERTY_QUESTION_ID, $question_id);
	}
	/**
	 * Returns the survey_id of this Dokeos185SharedSurveyQuestion.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Sets the survey_id of this Dokeos185SharedSurveyQuestion.
	 * @param survey_id
	 */
	function set_survey_id($survey_id)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_ID, $survey_id);
	}
	/**
	 * Returns the survey_question of this Dokeos185SharedSurveyQuestion.
	 * @return the survey_question.
	 */
	function get_survey_question()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_QUESTION);
	}

	/**
	 * Sets the survey_question of this Dokeos185SharedSurveyQuestion.
	 * @param survey_question
	 */
	function set_survey_question($survey_question)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_QUESTION, $survey_question);
	}
	/**
	 * Returns the survey_question_comment of this Dokeos185SharedSurveyQuestion.
	 * @return the survey_question_comment.
	 */
	function get_survey_question_comment()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_QUESTION_COMMENT);
	}

	/**
	 * Sets the survey_question_comment of this Dokeos185SharedSurveyQuestion.
	 * @param survey_question_comment
	 */
	function set_survey_question_comment($survey_question_comment)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_QUESTION_COMMENT, $survey_question_comment);
	}
	/**
	 * Returns the type of this Dokeos185SharedSurveyQuestion.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this Dokeos185SharedSurveyQuestion.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	/**
	 * Returns the display of this Dokeos185SharedSurveyQuestion.
	 * @return the display.
	 */
	function get_display()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY);
	}

	/**
	 * Sets the display of this Dokeos185SharedSurveyQuestion.
	 * @param display
	 */
	function set_display($display)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY, $display);
	}
	/**
	 * Returns the sort of this Dokeos185SharedSurveyQuestion.
	 * @return the sort.
	 */
	function get_sort()
	{
		return $this->get_default_property(self :: PROPERTY_SORT);
	}

	/**
	 * Sets the sort of this Dokeos185SharedSurveyQuestion.
	 * @param sort
	 */
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
	/**
	 * Returns the code of this Dokeos185SharedSurveyQuestion.
	 * @return the code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}

	/**
	 * Sets the code of this Dokeos185SharedSurveyQuestion.
	 * @param code
	 */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	/**
	 * Returns the max_value of this Dokeos185SharedSurveyQuestion.
	 * @return the max_value.
	 */
	function get_max_value()
	{
		return $this->get_default_property(self :: PROPERTY_MAX_VALUE);
	}

	/**
	 * Sets the max_value of this Dokeos185SharedSurveyQuestion.
	 * @param max_value
	 */
	function set_max_value($max_value)
	{
		$this->set_default_property(self :: PROPERTY_MAX_VALUE, $max_value);
	}

}

?>