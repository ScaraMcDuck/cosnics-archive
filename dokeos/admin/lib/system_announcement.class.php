<?php
/**
 * @package admin.lib
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__).'/admin_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';

class SystemAnnouncement
{
	const PROPERTY_ID = 'id';
	const PROPERTY_LEARNING_OBJECT_ID = 'learning_object';
	const PROPERTY_FROM_DATE = 'from_date';
	const PROPERTY_TO_DATE = 'to_date';
	const PROPERTY_HIDDEN = 'hidden';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';
	const PROPERTY_MODIFIED = 'modified';
	const PROPERTY_DISPLAY_ORDER = 'display_order';
	const PROPERTY_EMAIL_SENT = 'email_sent';

	private $id;
	private $defaultProperties;
	
	function LearningObjectPublication($id = 0, $defaultProperties = array ())
	{
		$this->id = $id;
		$this->defaultProperties = $defaultProperties;
	}
	
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_LEARNING_OBJECT_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_MODIFIED, self :: PROPERTY_DISPLAY_ORDER, self :: PROPERTY_EMAIL_SENT);
	}
	
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	function get_id()
	{
		return $this->id;
	}
	
	function get_learning_object_id()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT_ID);
	}
	
	function get_from_date()
	{
		return $this->get_default_property(self :: PROPERTY_FROM_DATE);
	}
	
	function get_to_date()
	{
		return $this->get_default_property(self :: PROPERTY_TO_DATE);
	}
	
	function get_hidden()
	{
		return $this->get_default_property(self :: PROPERTY_HIDDEN);
	}
	
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}
	
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}
	
	function get_modified()
	{
		return $this->get_default_property(self :: PROPERTY_MODIFIED);
	} 
	
	function get_display_order()
	{
		return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER);
	}
	
	function get_email_sent()
	{
		return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
	}
	
	function set_id($id)
	{
		$this->id = $id;
	}	
	
	function set_learning_object_id($id)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT_ID, $id);
	}
	
	function set_from_date($from_date)
	{
		$this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
	}
	
	function set_to_date($to_date)
	{
		$this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
	}
	
	function set_hidden($hidden)
	{
		$this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
	}
	
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
	
	function set_modified($modified)
	{
		$this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
	}
	
	function set_display_order($display_order)
	{
		$this->set_default_property(self :: PROPERTY_DISPLAY_ORDER, $display_order);
	}
	
	function set_email_sent($email_sent)
	{
		$this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
	}
	
	function get_publication_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		return $rdm->retrieve_learning_object($this->get_learning_object_id());
	}
	
	function get_publication_publisher()
	{
		$udm = UsersDataManager :: get_instance();
		return $udm->retrieve_user($this->get_publisher());
	}
	
	function was_email_sent()
	{
		return $this->get_email_sent();
	}
	
	function create()
	{
		$now = time();
		$this->set_published($now);
		$adm = AdminDataManager :: get_instance();
		$id = $adm->get_next_system_announcement_id();
		$this->set_id($id);
		return $adm->create_system_announcement($this);
	}
	
	function delete()
	{
		return AdminDataManager :: get_instance()->delete_system_announcement($this);
	}
	
	function update()
	{
		return AdminDataManager :: get_instance()->update_system_announcement($this);
	}
}
?>
