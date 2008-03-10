<?php

/**
 * @package migration.platform.dokeos185
 */
 
require_once dirname(__FILE__) . '/../../lib/import/importannouncement.class.php';
require_once dirname(__FILE__) . '/../../../repository/lib/learning_object/announcement/announcement.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once 'dokeos185itemproperty.class.php';

/**
 * This class represents an old Dokeos 1.8.5 announcement
 *
 * @author Sven Vanpoucke
 */
class Dokeos185Announcement extends ImportAnnouncement
{
	/**
	 * Migration data manager
	 */
	private static $mgdm,$item_property;

	/**
	 * Announcement properties
	 */	 
	const PROPERTY_ID = 'id';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_CONTENT = 'content';
	const PROPERTY_END_DATE = 'end_date';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_EMAIL_SENT = 'email_sent';
	
	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new dokeos185 Announcement object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185Announcement($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_TITLE, self :: PROPERTY_CONTENT,
					  self :: PROPERTY_END_DATE, self :: PROPERTY_DISPLAY_ORDER, 
					  self :: PROPERTY_EMAIL_SENT);
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
	 * Returns the id of this announcement.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	 
	/**
	 * Returns the title of this announcement.
	 * @return string the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the content of this announcement.
	 * @return string the content.
	 */
	function get_content()
	{
		return $this->get_default_property(self :: PROPERTY_CONTENT);
	}
	
	/**
	 * Returns the end_date of this announcement.
	 * @return date the end_date.
	 */
	function get_end_date()
	{
		return $this->get_default_property(self :: PROPERTY_END_DATE);
	}
	
	/**
	 * Returns the display_order of this announcement.
	 * @return int the display_order.
	 */
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	/**
	 * Returns the email_sent of this announcement.
	 * @return int the email_sent.
	 */
	function get_email_sent()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
	}
	
	/**
	 * Sets the id of this announcement.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the title of this announcement.
	 * @param string $title The title
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	/**
	 * Sets the content of this announcement.
	 * @param string $content The content
	 */
	function set_content($content)
	{
		$this->set_default_property(self :: PROPERTY_CONTENT, $content);
	}
	
	/**
	 * Sets the end_date of this announcement.
	 * @param string $end_date The end_date
	 */
	function set_end_date($end_date)
	{
		$this->set_default_property(self :: PROPERTY_END_DATE, $end_date);
	}
	
	/**
	 * Sets the display_order of this announcement.
	 * @param string $display_order The display_order
	 */
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	/**
	 * Sets the email_sent of this announcement.
	 * @param string $email_sent The email_sent
	 */
	function set_email_sent($email_sent)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
	}
	
	function is_valid_announcement($course)
	{
		$this->item_property = self :: $mgdm->get_item_property($course->get_db_name(),'announcement',$this->get_id());	
	
		if(!$this->get_id() || !$this->get_display_order() || !$this->get_title() || $this->get_content()
			|| $item_property->get_user()!=0 || !$item_property->get_insert_date())
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.announcement');
			return false;
		}
		return true;
	}
	
	function convert_to_new_announcement($new_code)
	{
		$new_user_id = self :: $mgdm->get_id_reference($item_property->get_insert_user_id(),'user_user');	
	
		//announcement parameters
		$lcms_announcement = new Announcement();
		
		$lcms_announcement->set_title($this->get_title());
		$lcms_announcement->set_description($this->get_content());
		
		$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
			'category', Translation :: get_lang('MyRepository'));
		$lcms_announcement->set_parent_id($repository_id);
		
		//itemproperty
		
		
		$lcms_announcement->set_owner_id($new_user_id);
		$lcms_announcement->set_creation_date($item_property->get_insert_date());
		$lcms_announcement->set_modified_date($item_property->get_lastedit_date());
		
		//create announcement in database
		$lcms_announcement->create();
		
		//publication
		$publication = new LearningObjectPublication();
		
		$publication->set_learning_object($lcms_announcement->get_id());
		$publication->set_course(self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course'));
		$publication->set_tool('announcement');
		$publication->set_publisher($new_user_id);
		$publication->set_from_date($item_property->get_start_visible());
		$publication->set_to_date($item_property->get_end_visible());
		$publication->set_publication_date($item_property->get_insert_date());
		$publication->set_modified_date($item_property->get_modified_date());
		$publication->set_display_order_index($this->get_display_order());
		
		if ($item_property->get_visibility() == 1)
			$publication->set_hidden(0);
		else
			$publication->set_hidden(1);
		
		//create publication in database
		$publication->create();
		
		return $lcms_announcement;
	}
	
	static function get_all_announcements($mgdm,$db)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_announcements($db);
	}
	
}
?>
