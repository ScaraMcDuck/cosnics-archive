<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 assignment_submission
 *
 * @author Sven Vanpoucke
 */
class Dokeos185AssignmentSubmission
{
	/**
	 * Dokeos185AssignmentSubmission properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ASSIGNMENT_ID = 'assignment_id';
	const PROPERTY_PARENT_ID = 'parent_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_GROUP_ID = 'group_id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_VISIBILITY = 'visibility';
	const PROPERTY_CREATION_DATE = 'creation_date';
	const PROPERTY_LAST_EDIT_DATE = 'last_edit_date';
	const PROPERTY_AUTHORS = 'authors';
	const PROPERTY_SUBMITTED_TEXT = 'submitted_text';
	const PROPERTY_SUBMITTED_DOC_PATH = 'submitted_doc_path';
	const PROPERTY_PRIVATE_FEEDBACK = 'private_feedback';
	const PROPERTY_ORIGINAL_AUTH_ID = 'original_auth_id';
	const PROPERTY_SCORE = 'score';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185AssignmentSubmission object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185AssignmentSubmission($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_ASSIGNMENT_ID, self :: PROPERTY_PARENT_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_GROUP_ID, self :: PROPERTY_TITLE, self :: PROPERTY_VISIBILITY, self :: PROPERTY_CREATION_DATE, self :: PROPERTY_LAST_EDIT_DATE, self :: PROPERTY_AUTHORS, self :: PROPERTY_SUBMITTED_TEXT, self :: PROPERTY_SUBMITTED_DOC_PATH, self :: PROPERTY_PRIVATE_FEEDBACK, self :: PROPERTY_ORIGINAL_AUTH_ID, self :: PROPERTY_SCORE);
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
	 * Returns the id of this Dokeos185AssignmentSubmission.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the assignment_id of this Dokeos185AssignmentSubmission.
	 * @return the assignment_id.
	 */
	function get_assignment_id()
	{
		return $this->get_default_property(self :: PROPERTY_ASSIGNMENT_ID);
	}

	/**
	 * Returns the parent_id of this Dokeos185AssignmentSubmission.
	 * @return the parent_id.
	 */
	function get_parent_id()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT_ID);
	}

	/**
	 * Returns the user_id of this Dokeos185AssignmentSubmission.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Returns the group_id of this Dokeos185AssignmentSubmission.
	 * @return the group_id.
	 */
	function get_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_GROUP_ID);
	}

	/**
	 * Returns the title of this Dokeos185AssignmentSubmission.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the visibility of this Dokeos185AssignmentSubmission.
	 * @return the visibility.
	 */
	function get_visibility()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBILITY);
	}

	/**
	 * Returns the creation_date of this Dokeos185AssignmentSubmission.
	 * @return the creation_date.
	 */
	function get_creation_date()
	{
		return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
	}

	/**
	 * Returns the last_edit_date of this Dokeos185AssignmentSubmission.
	 * @return the last_edit_date.
	 */
	function get_last_edit_date()
	{
		return $this->get_default_property(self :: PROPERTY_LAST_EDIT_DATE);
	}

	/**
	 * Returns the authors of this Dokeos185AssignmentSubmission.
	 * @return the authors.
	 */
	function get_authors()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHORS);
	}

	/**
	 * Returns the submitted_text of this Dokeos185AssignmentSubmission.
	 * @return the submitted_text.
	 */
	function get_submitted_text()
	{
		return $this->get_default_property(self :: PROPERTY_SUBMITTED_TEXT);
	}

	/**
	 * Returns the submitted_doc_path of this Dokeos185AssignmentSubmission.
	 * @return the submitted_doc_path.
	 */
	function get_submitted_doc_path()
	{
		return $this->get_default_property(self :: PROPERTY_SUBMITTED_DOC_PATH);
	}

	/**
	 * Returns the private_feedback of this Dokeos185AssignmentSubmission.
	 * @return the private_feedback.
	 */
	function get_private_feedback()
	{
		return $this->get_default_property(self :: PROPERTY_PRIVATE_FEEDBACK);
	}

	/**
	 * Returns the original_auth_id of this Dokeos185AssignmentSubmission.
	 * @return the original_auth_id.
	 */
	function get_original_auth_id()
	{
		return $this->get_default_property(self :: PROPERTY_ORIGINAL_AUTH_ID);
	}

	/**
	 * Returns the score of this Dokeos185AssignmentSubmission.
	 * @return the score.
	 */
	function get_score()
	{
		return $this->get_default_property(self :: PROPERTY_SCORE);
	}


}

?>