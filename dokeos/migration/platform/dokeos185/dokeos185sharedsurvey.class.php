<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 shared_survey
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SharedSurvey
{
	/**
	 * Dokeos185SharedSurvey properties
	 */
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_CODE = 'code';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_SUBTITLE = 'subtitle';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_LANG = 'lang';
	const PROPERTY_TEMPLATE = 'template';
	const PROPERTY_INTRO = 'intro';
	const PROPERTY_SURVEYTHANKS = 'surveythanks';
	const PROPERTY_CREATION_DATE = 'creation_date';
	const PROPERTY_COURSE_CODE = 'course_code';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SharedSurvey object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SharedSurvey($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_SURVEY_ID, SELF :: PROPERTY_CODE, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_SUBTITLE, SELF :: PROPERTY_AUTHOR, SELF :: PROPERTY_LANG, SELF :: PROPERTY_TEMPLATE, SELF :: PROPERTY_INTRO, SELF :: PROPERTY_SURVEYTHANKS, SELF :: PROPERTY_CREATION_DATE, SELF :: PROPERTY_COURSE_CODE);
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
	 * Returns the survey_id of this Dokeos185SharedSurvey.
	 * @return the survey_id.
	 */
	function get_survey_id()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEY_ID);
	}

	/**
	 * Sets the survey_id of this Dokeos185SharedSurvey.
	 * @param survey_id
	 */
	function set_survey_id($survey_id)
	{
		$this->set_default_property(self :: PROPERTY_SURVEY_ID, $survey_id);
	}
	/**
	 * Returns the code of this Dokeos185SharedSurvey.
	 * @return the code.
	 */
	function get_code()
	{
		return $this->get_default_property(self :: PROPERTY_CODE);
	}

	/**
	 * Sets the code of this Dokeos185SharedSurvey.
	 * @param code
	 */
	function set_code($code)
	{
		$this->set_default_property(self :: PROPERTY_CODE, $code);
	}
	/**
	 * Returns the title of this Dokeos185SharedSurvey.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Dokeos185SharedSurvey.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	/**
	 * Returns the subtitle of this Dokeos185SharedSurvey.
	 * @return the subtitle.
	 */
	function get_subtitle()
	{
		return $this->get_default_property(self :: PROPERTY_SUBTITLE);
	}

	/**
	 * Sets the subtitle of this Dokeos185SharedSurvey.
	 * @param subtitle
	 */
	function set_subtitle($subtitle)
	{
		$this->set_default_property(self :: PROPERTY_SUBTITLE, $subtitle);
	}
	/**
	 * Returns the author of this Dokeos185SharedSurvey.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Sets the author of this Dokeos185SharedSurvey.
	 * @param author
	 */
	function set_author($author)
	{
		$this->set_default_property(self :: PROPERTY_AUTHOR, $author);
	}
	/**
	 * Returns the lang of this Dokeos185SharedSurvey.
	 * @return the lang.
	 */
	function get_lang()
	{
		return $this->get_default_property(self :: PROPERTY_LANG);
	}

	/**
	 * Sets the lang of this Dokeos185SharedSurvey.
	 * @param lang
	 */
	function set_lang($lang)
	{
		$this->set_default_property(self :: PROPERTY_LANG, $lang);
	}
	/**
	 * Returns the template of this Dokeos185SharedSurvey.
	 * @return the template.
	 */
	function get_template()
	{
		return $this->get_default_property(self :: PROPERTY_TEMPLATE);
	}

	/**
	 * Sets the template of this Dokeos185SharedSurvey.
	 * @param template
	 */
	function set_template($template)
	{
		$this->set_default_property(self :: PROPERTY_TEMPLATE, $template);
	}
	/**
	 * Returns the intro of this Dokeos185SharedSurvey.
	 * @return the intro.
	 */
	function get_intro()
	{
		return $this->get_default_property(self :: PROPERTY_INTRO);
	}

	/**
	 * Sets the intro of this Dokeos185SharedSurvey.
	 * @param intro
	 */
	function set_intro($intro)
	{
		$this->set_default_property(self :: PROPERTY_INTRO, $intro);
	}
	/**
	 * Returns the surveythanks of this Dokeos185SharedSurvey.
	 * @return the surveythanks.
	 */
	function get_surveythanks()
	{
		return $this->get_default_property(self :: PROPERTY_SURVEYTHANKS);
	}

	/**
	 * Sets the surveythanks of this Dokeos185SharedSurvey.
	 * @param surveythanks
	 */
	function set_surveythanks($surveythanks)
	{
		$this->set_default_property(self :: PROPERTY_SURVEYTHANKS, $surveythanks);
	}
	/**
	 * Returns the creation_date of this Dokeos185SharedSurvey.
	 * @return the creation_date.
	 */
	function get_creation_date()
	{
		return $this->get_default_property(self :: PROPERTY_CREATION_DATE);
	}

	/**
	 * Sets the creation_date of this Dokeos185SharedSurvey.
	 * @param creation_date
	 */
	function set_creation_date($creation_date)
	{
		$this->set_default_property(self :: PROPERTY_CREATION_DATE, $creation_date);
	}
	/**
	 * Returns the course_code of this Dokeos185SharedSurvey.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this Dokeos185SharedSurvey.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}

}

?>