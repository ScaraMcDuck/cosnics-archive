<?php 
/**
 * @package alexia
 */
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once dirname(__FILE__) . '/alexia_publication_user.class.php';
require_once dirname(__FILE__) . '/alexia_publication_group.class.php';

/**
 * This class describes an AlexiaPublication data object
 *
 * @author Hans De Bisschop
 */
class AlexiaPublication
{
	const CLASS_NAME = __CLASS__;
	const TABLE_NAME = 'publication';

	/**
	 * AlexiaPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_LEARNING_OBJECT = 'learning_object';
	const PROPERTY_FROM_DATE = 'from_date';
	const PROPERTY_TO_DATE = 'to_date';
	const PROPERTY_HIDDEN = 'hidden';
	const PROPERTY_PUBLISHER = 'publisher';
	const PROPERTY_PUBLISHED = 'published';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	private $target_groups;
	private $target_users;
	
	/**
	 * Creates a new AlexiaPublication object
	 * @param array $defaultProperties The default properties
	 */
	function AlexiaPublication($defaultProperties = array ())
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
		return array (self :: PROPERTY_ID, self :: PROPERTY_LEARNING_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED);
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
	 * Returns the id of this AlexiaPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Sets the id of this AlexiaPublication.
	 * @param id
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	/**
	 * Returns the learning_object of this AlexiaPublication.
	 * @return the learning_object.
	 */
	function get_learning_object()
	{
		return $this->get_default_property(self :: PROPERTY_LEARNING_OBJECT);
	}

	/**
	 * Sets the learning_object of this AlexiaPublication.
	 * @param learning_object
	 */
	function set_learning_object($learning_object)
	{
		$this->set_default_property(self :: PROPERTY_LEARNING_OBJECT, $learning_object);
	}
	/**
	 * Returns the from_date of this AlexiaPublication.
	 * @return the from_date.
	 */
	function get_from_date()
	{
		return $this->get_default_property(self :: PROPERTY_FROM_DATE);
	}

	/**
	 * Sets the from_date of this AlexiaPublication.
	 * @param from_date
	 */
	function set_from_date($from_date)
	{
		$this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
	}
	/**
	 * Returns the to_date of this AlexiaPublication.
	 * @return the to_date.
	 */
	function get_to_date()
	{
		return $this->get_default_property(self :: PROPERTY_TO_DATE);
	}

	/**
	 * Sets the to_date of this AlexiaPublication.
	 * @param to_date
	 */
	function set_to_date($to_date)
	{
		$this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
	}
	/**
	 * Returns the hidden of this AlexiaPublication.
	 * @return the hidden.
	 */
	function get_hidden()
	{
		return $this->get_default_property(self :: PROPERTY_HIDDEN);
	}

	/**
	 * Sets the hidden of this AlexiaPublication.
	 * @param hidden
	 */
	function set_hidden($hidden)
	{
		$this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
	}
	/**
	 * Returns the publisher of this AlexiaPublication.
	 * @return the publisher.
	 */
	function get_publisher()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHER);
	}

	/**
	 * Sets the publisher of this AlexiaPublication.
	 * @param publisher
	 */
	function set_publisher($publisher)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
	}
	/**
	 * Returns the published of this AlexiaPublication.
	 * @return the published.
	 */
	function get_published()
	{
		return $this->get_default_property(self :: PROPERTY_PUBLISHED);
	}

	/**
	 * Sets the published of this AlexiaPublication.
	 * @param published
	 */
	function set_published($published)
	{
		$this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
	}
	
	function set_target_groups($target_groups)
	{
		$this->target_groups = $target_groups;
	}
	
	function set_target_users($target_users)
	{
		$this->target_users = $target_users;
	}
	
	function get_target_groups()
	{
		if(!$this->target_groups)
		{
			$condition = new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_PUBLICATION, $this->get_id());
			$groups = AlexiaDataManager :: get_instance()->retrieve_alexia_publication_groups($condition);
			
			while($group = $groups->next_result())
			{
				$this->target_groups[] = $group->get_group_id();
			}
		}
		
		return $this->target_groups;
	}
	
	function get_target_users()
	{
		if(!$this->target_users)
		{
			$condition = new EqualityCondition(AlexiaPublicationUser :: PROPERTY_PUBLICATION, $this->get_id());
			$users = AlexiaDataManager :: get_instance()->retrieve_alexia_publication_users($condition);
			
			while($user = $users->next_result())
			{
				$this->target_users[] = $user->get_user();
			}
		}
		
		return $this->target_users;
	}
	
	function is_visible_for_target_user($user_id)
	{
		$user = UserDataManager :: get_instance()->retrieve_user($user_id);
		
		if($user->is_platform_admin() || $user_id == $this->get_publisher())
			return true;
		
		if($this->get_target_groups() || $this->get_target_users())
		{ 
			$allowed = false;
			
			if(in_array($user_id, $this->get_target_users()))
			{
				$allowed = true;
			}
			
			if(!$allowed)
			{
				$user_groups = $user->get_groups();

				while($user_group = $user_groups->next_result())
				{
					if(in_array($user_group->get_id(), $this->get_target_groups()))
					{
						$allowed = true;
						break;
					}
				}
			}

			if(!$allowed)
			{
				return false;
			}
		}
		
		if($this->get_hidden())
		{
			return false;
		}
		
		$time = time();
		
		if($time < $this->get_from_date() || $time > $this->get_to_date())
		{
			return false;
		}
		
		return true;
	}

	function delete()
	{
		$dm = AlexiaDataManager :: get_instance();
		return $dm->delete_alexia_publication($this);
	}

	function create()
	{
		$dm = AlexiaDataManager :: get_instance();
		$this->set_id($dm->get_next_alexia_publication_id());
       	return $dm->create_alexia_publication($this);
	}

	function update($delete_targets = true)
	{
		$dm = AlexiaDataManager :: get_instance();
		return $dm->update_alexia_publication($this, $delete_targets);
	}

	static function get_table_name()
	{
		return self :: TABLE_NAME;
	}
	
    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_learning_object($this->get_learning_object());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }
}
?>