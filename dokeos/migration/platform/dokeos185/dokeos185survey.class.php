<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 survey
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
	 * Sets the survey_id of this Dokeos185Survey.
	 * @param survey_id
	 */
	function set_survey_id($survey_id)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_ID, $survey_id);
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
	 * Sets the code of this Dokeos185Survey.
	 * @param code
	 */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
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
	 * Sets the title of this Dokeos185Survey.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
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
	 * Sets the subtitle of this Dokeos185Survey.
	 * @param subtitle
	 */
	function set_subtitle($subtitle)
	{
		$this->set_default_property(self :: PROPERTY_SUBTITLE, $subtitle);
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
	 * Sets the author of this Dokeos185Survey.
	 * @param author
	 */
	function set_author($author)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR, $author);
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
	 * Sets the lang of this Dokeos185Survey.
	 * @param lang
	 */
	function set_lang($lang)
	{
		$this->set_default_property(self :: PROPERTY_LANG, $lang);
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
	 * Sets the avail_from of this Dokeos185Survey.
	 * @param avail_from
	 */
	function set_avail_from($avail_from)
	{
		$this->set_default_property(self :: PROPERTY_AVAIL_FROM, $avail_from);
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
	 * Sets the avail_till of this Dokeos185Survey.
	 * @param avail_till
	 */
	function set_avail_till($avail_till)
	{
		$this->set_default_property(self :: PROPERTY_AVAIL_TILL, $avail_till);
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
	 * Sets the is_shared of this Dokeos185Survey.
	 * @param is_shared
	 */
	function set_is_shared($is_shared)
	{
		$this->set_default_property(self :: PROPERTY_IS_SHARED, $is_shared);
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
	 * Sets the template of this Dokeos185Survey.
	 * @param template
	 */
	function set_template($template)
	{
		$this->set_default_property(self :: PROPERTY_TEMPLATE, $template);
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
	 * Sets the intro of this Dokeos185Survey.
	 * @param intro
	 */
	function set_intro($intro)
	{
		$this->set_default_property(self :: PROPERTY_INTRO, $intro);
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
	 * Sets the surveythanks of this Dokeos185Survey.
	 * @param surveythanks
	 */
	function set_surveythanks($surveythanks)
	{
		$this->set_default_property(self :: PROPERTY_SURVEYTHANKS, $surveythanks);
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
	 * Sets the creation_date of this Dokeos185Survey.
	 * @param creation_date
	 */
	function set_creation_date($creation_date)
	{
		$this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
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
	 * Sets the invited of this Dokeos185Survey.
	 * @param invited
	 */
	function set_invited($invited)
	{
		$this->set_default_property(self :: PROPERTY_INVITED, $invited);
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
	 * Sets the answered of this Dokeos185Survey.
	 * @param answered
	 */
	function set_answered($answered)
	{
		$this->set_default_property(self :: PROPERTY_ANSWERED, $answered);
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
	 * Sets the invite_mail of this Dokeos185Survey.
	 * @param invite_mail
	 */
	function set_invite_mail($invite_mail)
	{
		$this->set_default_property(self :: PROPERTY_INVITE_MAIL, $invite_mail);
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
	 * Sets the reminder_mail of this Dokeos185Survey.
	 * @param reminder_mail
	 */
	function set_reminder_mail($reminder_mail)
	{
		$this->set_default_property(self :: PROPERTY_REMINDER_MAIL, $reminder_mail);
	}
	/**
	 * Returns the anonymous of this Dokeos185Survey.
	 * @return the anonymous.
	 */
	function get_anonymous()
	{
		return $this->get_default_property(self :: PROPERTY_ANONYMOUS);
	}

	/**
	 * Sets the anonymous of this Dokeos185Survey.
	 * @param anonymous
	 */
	function set_anonymous($anonymous)
	{
		$this->set_default_property(self :: PROPERTY_ANONYMOUS, $anonymous);
	}

}

?>