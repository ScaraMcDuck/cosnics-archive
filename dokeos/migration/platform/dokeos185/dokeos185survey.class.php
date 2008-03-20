<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 survey
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Survey
{
	/**
	 * Dokeos185Survey properties
	 */
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_CODE = 'code';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_SUBTITLE = 'subtitle';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_LANG = 'lang';
	const PROPERTY_AVAIL_FROM = 'avail_from';
	const PROPERTY_AVAIL_TILL = 'avail_till';
	const PROPERTY_IS_SHARED = 'is_shared';
	const PROPERTY_TEMPLATE = 'template';
	const PROPERTY_INTRO = 'intro';
	const PROPERTY_SURVEYTHANKS = 'surveythanks';
	const PROPERTY_CREATION_DATE = 'creation_date';
	const PROPERTY_INVITED = 'invited';
	const PROPERTY_ANSWERED = 'answered';
	const PROPERTY_INVITE_MAIL = 'invite_mail';
	const PROPERTY_REMINDER_MAIL = 'reminder_mail';
	const PROPERTY_ANONYMOUS = 'anonymous';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185Survey object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Survey($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_SURVEY_ID, SELF :: PROPERTY_CODE, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_SUBTITLE, SELF :: PROPERTY_AUTHOR, SELF :: PROPERTY_LANG, SELF :: PROPERTY_AVAIL_FROM, SELF :: PROPERTY_AVAIL_TILL, SELF :: PROPERTY_IS_SHARED, SELF :: PROPERTY_TEMPLATE, SELF :: PROPERTY_INTRO, SELF :: PROPERTY_SURVEYTHANKS, SELF :: PROPERTY_CREATION_DATE, SELF :: PROPERTY_INVITED, SELF :: PROPERTY_ANSWERED, SELF :: PROPERTY_INVITE_MAIL, SELF :: PROPERTY_REMINDER_MAIL, SELF :: PROPERTY_ANONYMOUS);
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
	 * Returns the survey_id of this Dokeos185Survey.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Returns the code of this Dokeos185Survey.
	 * @return the code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}

	/**
	 * Returns the title of this Dokeos185Survey.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the subtitle of this Dokeos185Survey.
	 * @return the subtitle.
	 */
	function get_subtitle()
	{
		return $this->get_default_property(self :: PROPERTY_SUBTITLE);
	}

	/**
	 * Returns the author of this Dokeos185Survey.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Returns the lang of this Dokeos185Survey.
	 * @return the lang.
	 */
	function get_lang()
	{
		return $this->get_default_property(self :: PROPERTY_LANG);
	}

	/**
	 * Returns the avail_from of this Dokeos185Survey.
	 * @return the avail_from.
	 */
	function get_avail_from()
	{
		return $this->get_default_property(self :: PROPERTY_AVAIL_FROM);
	}

	/**
	 * Returns the avail_till of this Dokeos185Survey.
	 * @return the avail_till.
	 */
	function get_avail_till()
	{
		return $this->get_default_property(self :: PROPERTY_AVAIL_TILL);
	}

	/**
	 * Returns the is_shared of this Dokeos185Survey.
	 * @return the is_shared.
	 */
	function get_is_shared()
	{
		return $this->get_default_property(self :: PROPERTY_IS_SHARED);
	}

	/**
	 * Returns the template of this Dokeos185Survey.
	 * @return the template.
	 */
	function get_template()
	{
		return $this->get_default_property(self :: PROPERTY_TEMPLATE);
	}

	/**
	 * Returns the intro of this Dokeos185Survey.
	 * @return the intro.
	 */
	function get_intro()
	{
		return $this->get_default_property(self :: PROPERTY_INTRO);
	}

	/**
	 * Returns the surveythanks of this Dokeos185Survey.
	 * @return the surveythanks.
	 */
	function get_surveythanks()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEYTHANKS);
	}

	/**
	 * Returns the creation_date of this Dokeos185Survey.
	 * @return the creation_date.
	 */
	function get_creation_date()
	{
		return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
	}

	/**
	 * Returns the invited of this Dokeos185Survey.
	 * @return the invited.
	 */
	function get_invited()
	{
		return $this->get_default_property(self :: PROPERTY_INVITED);
	}

	/**
	 * Returns the answered of this Dokeos185Survey.
	 * @return the answered.
	 */
	function get_answered()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWERED);
	}

	/**
	 * Returns the invite_mail of this Dokeos185Survey.
	 * @return the invite_mail.
	 */
	function get_invite_mail()
	{
		return $this->get_default_property(self :: PROPERTY_INVITE_MAIL);
	}

	/**
	 * Returns the reminder_mail of this Dokeos185Survey.
	 * @return the reminder_mail.
	 */
	function get_reminder_mail()
	{
		return $this->get_default_property(self :: PROPERTY_REMINDER_MAIL);
	}

	/**
	 * Returns the anonymous of this Dokeos185Survey.
	 * @return the anonymous.
	 */
	function get_anonymous()
	{
		return $this->get_default_property(self :: PROPERTY_ANONYMOUS);
	}
	
	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];

		if($array['del_files'] =! 1)
			$tool_name = 'survey';
		
		$coursedb = $array['course'];
		$tablename = 'survey';
		$classname = 'Dokeos185Survey';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}

}

?>