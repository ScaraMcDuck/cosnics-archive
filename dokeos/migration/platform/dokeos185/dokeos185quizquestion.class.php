<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 quiz_question
 *
 * @author Sven Vanpoucke
 */
class Dokeos185QuizQuestion
{
	/**
	 * Dokeos185QuizQuestion properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_QUESTION = 'question';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_PONDERATION = 'ponderation';
	const PROPERTY_POSITION = 'position';
	const PROPERTY_TYPE = 'type';
	const PROPERTY_PICTURE = 'picture';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185QuizQuestion object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185QuizQuestion($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_QUESTION, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_PONDERATION, SELF :: PROPERTY_POSITION, SELF :: PROPERTY_TYPE, SELF :: PROPERTY_PICTURE);
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
	 * Returns the id of this Dokeos185QuizQuestion.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185QuizQuestion.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the question of this Dokeos185QuizQuestion.
	 * @return the question.
	 */
	function get_question()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION);
	}

	/**
	 * Sets the question of this Dokeos185QuizQuestion.
	 * @param question
	 */
	function set_question($question)
	{
		$this->set_default_property(self :: PROPERTY_QUESTION, $question);
	}
	/**
	 * Returns the description of this Dokeos185QuizQuestion.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Sets the description of this Dokeos185QuizQuestion.
	 * @param description
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	/**
	 * Returns the ponderation of this Dokeos185QuizQuestion.
	 * @return the ponderation.
	 */
	function get_ponderation()
	{
		return $this->get_default_property(self :: PROPERTY_PONDERATION);
	}

	/**
	 * Sets the ponderation of this Dokeos185QuizQuestion.
	 * @param ponderation
	 */
	function set_ponderation($ponderation)
	{
		$this->set_default_property(self :: PROPERTY_PONDERATION, $ponderation);
	}
	/**
	 * Returns the position of this Dokeos185QuizQuestion.
	 * @return the position.
	 */
	function get_position()
	{
		return $this->get_default_property(self :: PROPERTY_POSITION);
	}

	/**
	 * Sets the position of this Dokeos185QuizQuestion.
	 * @param position
	 */
	function set_position($position)
	{
		$this->set_default_property(self :: PROPERTY_POSITION, $position);
	}
	/**
	 * Returns the type of this Dokeos185QuizQuestion.
	 * @return the type.
	 */
	function get_type()
	{
		return $this->get_default_property(self :: PROPERTY_TYPE);
	}

	/**
	 * Sets the type of this Dokeos185QuizQuestion.
	 * @param type
	 */
	function set_type($type)
	{
		$this->set_default_property(self :: PROPERTY_TYPE, $type);
	}
	/**
	 * Returns the picture of this Dokeos185QuizQuestion.
	 * @return the picture.
	 */
	function get_picture()
	{
		return $this->get_default_property(self :: PROPERTY_PICTURE);
	}

	/**
	 * Sets the picture of this Dokeos185QuizQuestion.
	 * @param picture
	 */
	function set_picture($picture)
	{
		$this->set_default_property(self :: PROPERTY_PICTURE, $picture);
	}

}

?>