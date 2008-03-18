<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 dropbox_feedback
 *
 * @author Sven Vanpoucke
 */
class Dokeos185DropboxFeedback
{
	/**
	 * Dokeos185DropboxFeedback properties
	 */
	const PROPERTY_FEEDBACK_ID = 'feedback_id';
	const PROPERTY_FILE_ID = 'file_id';
	const PROPERTY_AUTHOR_USER_ID = 'author_user_id';
	const PROPERTY_FEEDBACK = 'feedback';
	const PROPERTY_FEEDBACK_DATE = 'feedback_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185DropboxFeedback object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185DropboxFeedback($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_FEEDBACK_ID, SELF :: PROPERTY_FILE_ID, SELF :: PROPERTY_AUTHOR_USER_ID, SELF :: PROPERTY_FEEDBACK, SELF :: PROPERTY_FEEDBACK_DATE);
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
	 * Returns the feedback_id of this Dokeos185DropboxFeedback.
	 * @return the feedback_id.
	 */
	function get_feedback_id()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_ID);
	}

	/**
	 * Returns the file_id of this Dokeos185DropboxFeedback.
	 * @return the file_id.
	 */
	function get_file_id()
	{
		return $this->get_default_property(self :: PROPERTY_FILE_ID);
	}

	/**
	 * Returns the author_user_id of this Dokeos185DropboxFeedback.
	 * @return the author_user_id.
	 */
	function get_author_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR_USER_ID);
	}

	/**
	 * Returns the feedback of this Dokeos185DropboxFeedback.
	 * @return the feedback.
	 */
	function get_feedback()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK);
	}

	/**
	 * Returns the feedback_date of this Dokeos185DropboxFeedback.
	 * @return the feedback_date.
	 */
	function get_feedback_date()
	{
		return $this->get_default_property(self :: PROPERTY_FEEDBACK_DATE);
	}


}

?>