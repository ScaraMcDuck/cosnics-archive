<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 sys_announcement
 *
 * @author Sven Vanpoucke
 */
class Dokeos185SysAnnouncement
{
	/**
	 * Dokeos185SysAnnouncement properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_DATE_START = 'date_start';
	const PROPERTY_DATE_END = 'date_end';
	const PROPERTY_VISIBLE_TEACHER = 'visible_teacher';
	const PROPERTY_VISIBLE_STUDENT = 'visible_student';
	const PROPERTY_VISIBLE_GUEST = 'visible_guest';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_CONTENT = 'content';
	const PROPERTY_LANG = 'lang';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185SysAnnouncement object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185SysAnnouncement($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_DATE_START, SELF :: PROPERTY_DATE_END, SELF :: PROPERTY_VISIBLE_TEACHER, SELF :: PROPERTY_VISIBLE_STUDENT, SELF :: PROPERTY_VISIBLE_GUEST, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_CONTENT, SELF :: PROPERTY_LANG);
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
	 * Returns the id of this Dokeos185SysAnnouncement.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this Dokeos185SysAnnouncement.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the date_start of this Dokeos185SysAnnouncement.
	 * @return the date_start.
	 */
	function get_date_start()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_START);
	}

	/**
	 * Sets the date_start of this Dokeos185SysAnnouncement.
	 * @param date_start
	 */
	function set_date_start($date_start)
	{
		$this->set_default_property(self :: PROPERTY_DATE_START, $date_start);
	}
	/**
	 * Returns the date_end of this Dokeos185SysAnnouncement.
	 * @return the date_end.
	 */
	function get_date_end()
	{
		return $this->get_default_property(self :: PROPERTY_DATE_END);
	}

	/**
	 * Sets the date_end of this Dokeos185SysAnnouncement.
	 * @param date_end
	 */
	function set_date_end($date_end)
	{
		$this->set_default_property(self :: PROPERTY_DATE_END, $date_end);
	}
	/**
	 * Returns the visible_teacher of this Dokeos185SysAnnouncement.
	 * @return the visible_teacher.
	 */
	function get_visible_teacher()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE_TEACHER);
	}

	/**
	 * Sets the visible_teacher of this Dokeos185SysAnnouncement.
	 * @param visible_teacher
	 */
	function set_visible_teacher($visible_teacher)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE_TEACHER, $visible_teacher);
	}
	/**
	 * Returns the visible_student of this Dokeos185SysAnnouncement.
	 * @return the visible_student.
	 */
	function get_visible_student()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE_STUDENT);
	}

	/**
	 * Sets the visible_student of this Dokeos185SysAnnouncement.
	 * @param visible_student
	 */
	function set_visible_student($visible_student)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE_STUDENT, $visible_student);
	}
	/**
	 * Returns the visible_guest of this Dokeos185SysAnnouncement.
	 * @return the visible_guest.
	 */
	function get_visible_guest()
	{
		return $this->get_default_property(self :: PROPERTY_VISIBLE_GUEST);
	}

	/**
	 * Sets the visible_guest of this Dokeos185SysAnnouncement.
	 * @param visible_guest
	 */
	function set_visible_guest($visible_guest)
	{
		$this->set_default_property(self :: PROPERTY_VISIBLE_GUEST, $visible_guest);
	}
	/**
	 * Returns the title of this Dokeos185SysAnnouncement.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Sets the title of this Dokeos185SysAnnouncement.
	 * @param title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	/**
	 * Returns the content of this Dokeos185SysAnnouncement.
	 * @return the content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}

	/**
	 * Sets the content of this Dokeos185SysAnnouncement.
	 * @param content
	 */
	function set_content($content)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT, $content);
	}
	/**
	 * Returns the lang of this Dokeos185SysAnnouncement.
	 * @return the lang.
	 */
	function get_lang()
	{
		return $this->get_default_property(self :: PROPERTY_LANG);
	}

	/**
	 * Sets the lang of this Dokeos185SysAnnouncement.
	 * @param lang
	 */
	function set_lang($lang)
	{
		$this->set_default_property(self :: PROPERTY_LANG, $lang);
	}

}

?>