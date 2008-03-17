<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 survey_invitation
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SurveyInvitation
{
	/**
	 * Dokeos185SurveyInvitation properties
	 */
	const PROPERTY_SURVEY_INVITATION_ID = 'survey_invitation_id';
	const PROPERTY_SURVEY_CODE = 'survey_code';
	const PROPERTY_USER = 'user';
	const PROPERTY_INVITATION_CODE = 'invitation_code';
	const PROPERTY_INVITATION_DATE = 'invitation_date';
	const PROPERTY_REMINDER_DATE = 'reminder_date';
	const PROPERTY_ANSWERED = 'answered';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SurveyInvitation object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SurveyInvitation($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_SURVEY_INVITATION_ID, SELF :: PROPERTY_SURVEY_CODE, SELF :: PROPERTY_USER, SELF :: PROPERTY_INVITATION_CODE, SELF :: PROPERTY_INVITATION_DATE, SELF :: PROPERTY_REMINDER_DATE, SELF :: PROPERTY_ANSWERED);
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
	 * Returns the survey_invitation_id of this Dokeos185SurveyInvitation.
	 * @return the survey_invitation_id.
	 */
	function get_survey_invitation_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_INVITATION_ID);
	}

	/**
	 * Sets the survey_invitation_id of this Dokeos185SurveyInvitation.
	 * @param survey_invitation_id
	 */
	function set_survey_invitation_id($survey_invitation_id)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_INVITATION_ID, $survey_invitation_id);
	}
	/**
	 * Returns the survey_code of this Dokeos185SurveyInvitation.
	 * @return the survey_code.
	 */
	function get_survey_code()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_CODE);
	}

	/**
	 * Sets the survey_code of this Dokeos185SurveyInvitation.
	 * @param survey_code
	 */
	function set_survey_code($survey_code)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_CODE, $survey_code);
	}
	/**
	 * Returns the user of this Dokeos185SurveyInvitation.
	 * @return the user.
	 */
	function get_user()
	{
		return $this->get_default_property(self :: PROPERTY_USER);
	}

	/**
	 * Sets the user of this Dokeos185SurveyInvitation.
	 * @param user
	 */
	function set_user($user)
	{
		$this->set_default_property(self :: PROPERTY_USER, $user);
	}
	/**
	 * Returns the invitation_code of this Dokeos185SurveyInvitation.
	 * @return the invitation_code.
	 */
	function get_invitation_code()
	{
		return $this->get_default_property(self :: PROPERTY_INVITATION_CODE);
	}

	/**
	 * Sets the invitation_code of this Dokeos185SurveyInvitation.
	 * @param invitation_code
	 */
	function set_invitation_code($invitation_code)
	{
		$this->set_default_property(self :: PROPERTY_INVITATION_CODE, $invitation_code);
	}
	/**
	 * Returns the invitation_date of this Dokeos185SurveyInvitation.
	 * @return the invitation_date.
	 */
	function get_invitation_date()
	{
		return $this->get_default_property(self :: PROPERTY_INVITATION_DATE);
	}

	/**
	 * Sets the invitation_date of this Dokeos185SurveyInvitation.
	 * @param invitation_date
	 */
	function set_invitation_date($invitation_date)
	{
		$this->set_default_property(self :: PROPERTY_INVITATION_DATE, $invitation_date);
	}
	/**
	 * Returns the reminder_date of this Dokeos185SurveyInvitation.
	 * @return the reminder_date.
	 */
	function get_reminder_date()
	{
		return $this->get_default_property(self :: PROPERTY_REMINDER_DATE);
	}

	/**
	 * Sets the reminder_date of this Dokeos185SurveyInvitation.
	 * @param reminder_date
	 */
	function set_reminder_date($reminder_date)
	{
		$this->set_default_property(self :: PROPERTY_REMINDER_DATE, $reminder_date);
	}
	/**
	 * Returns the answered of this Dokeos185SurveyInvitation.
	 * @return the answered.
	 */
	function get_answered()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWERED);
	}

	/**
	 * Sets the answered of this Dokeos185SurveyInvitation.
	 * @param answered
	 */
	function set_answered($answered)
	{
		$this->set_default_property(self :: PROPERTY_ANSWERED, $answered);
	}

}

?>