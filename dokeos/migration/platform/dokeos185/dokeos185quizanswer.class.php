<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 quiz_answer
 *
 * @author Sven Vanpoucke
 */
class Dokeos185QuizAnswer
{
	/**
	 * Dokeos185QuizAnswer properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_ANSWER = 'answer';
	const PROPERTY_CORRECT = 'correct';
	const PROPERTY_COMMENT = 'comment';
	const PROPERTY_PONDERATION = 'ponderation';
	const PROPERTY_POSITION = 'position';
	const PROPERTY_HOTSPOT_COORDINATES = 'hotspot_coordinates';
	const PROPERTY_HOTSPOT_TYPE = 'hotspot_type';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185QuizAnswer object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185QuizAnswer($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_QUESTION_ID, SELF :: PROPERTY_ANSWER, SELF :: PROPERTY_CORRECT, SELF :: PROPERTY_COMMENT, SELF :: PROPERTY_PONDERATION, SELF :: PROPERTY_POSITION, SELF :: PROPERTY_HOTSPOT_COORDINATES, SELF :: PROPERTY_HOTSPOT_TYPE);
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
	 * Returns the id of this Dokeos185QuizAnswer.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the question_id of this Dokeos185QuizAnswer.
	 * @return the question_id.
	 */
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}

	/**
	 * Returns the answer of this Dokeos185QuizAnswer.
	 * @return the answer.
	 */
	function get_answer()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWER);
	}

	/**
	 * Returns the correct of this Dokeos185QuizAnswer.
	 * @return the correct.
	 */
	function get_correct()
	{
		return $this->get_default_property(self :: PROPERTY_CORRECT);
	}

	/**
	 * Returns the comment of this Dokeos185QuizAnswer.
	 * @return the comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}

	/**
	 * Returns the ponderation of this Dokeos185QuizAnswer.
	 * @return the ponderation.
	 */
	function get_ponderation()
	{
		return $this->get_default_property(self :: PROPERTY_PONDERATION);
	}

	/**
	 * Returns the position of this Dokeos185QuizAnswer.
	 * @return the position.
	 */
	function get_position()
	{
		return $this->get_default_property(self :: PROPERTY_POSITION);
	}

	/**
	 * Returns the hotspot_coordinates of this Dokeos185QuizAnswer.
	 * @return the hotspot_coordinates.
	 */
	function get_hotspot_coordinates()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_COORDINATES);
	}

	/**
	 * Returns the hotspot_type of this Dokeos185QuizAnswer.
	 * @return the hotspot_type.
	 */
	function get_hotspot_type()
	{
		return $this->get_default_property(self :: PROPERTY_HOTSPOT_TYPE);
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];

		if($array['del_files'] =! 1)
			$tool_name = 'quiz_answer';
		
		$coursedb = $array['course'];
		$tablename = 'quiz_answer';
		$classname = 'Dokeos185QuizAnswer';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}


}

?>