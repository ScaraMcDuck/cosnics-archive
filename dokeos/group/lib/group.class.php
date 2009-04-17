<?php

require_once dirname(__FILE__).'/group_data_manager.class.php';

/**
 * @package group
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 *  @author Sven Vanpoucke
 */

class Group
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_ID = 'id';
	const PROPERTY_NAME = 'name';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_SORT = 'sort';
	const PROPERTY_PARENT = 'parent';
	const PROPERTY_LEFT_VALUE = 'left_value';
	const PROPERTY_RIGHT_VALUE = 'right_value';
	
	/**
	 * Default properties of the group object, stored in an associative
	 * array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new group object.
	 * @param int $id The numeric ID of the group object. May be omitted
	 *                if creating a new object.
	 * @param array $defaultProperties The default properties of the group
	 *                                 object. Associative array.
	 */
	function Group($id = 0, $defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this group object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this group.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all groups.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_NAME, self :: PROPERTY_DESCRIPTION, self :: PROPERTY_SORT, self :: PROPERTY_PARENT, self :: PROPERTY_LEFT_VALUE, self :: PROPERTY_RIGHT_VALUE);
	}
		
	/**
	 * Sets a default property of this group by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default group
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}

	/**
	 * Returns the id of this group.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the name of this group.
	 * @return String The name
	 */
	function get_name()
	{
		return $this->get_default_property(self :: PROPERTY_NAME);
	}
	
	/**
	 * Returns the description of this group.
	 * @return String The description
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}
	
	function get_sort()
	{
		return $this->get_default_property(self :: PROPERTY_SORT);
	}
	
	/**
	 * Sets the group_id of this group.
	 * @param int $group_id The group_id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}		
	
	/**
	 * Sets the name of this group.
	 * @param String $name the name.
	 */
	function set_name($name)
	{
		$this->set_default_property(self :: PROPERTY_NAME, $name);
	}
	
	/**
	 * Sets the description of this group.
	 * @param String $description the description.
	 */
	function set_description($description)
	{
		$this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
	}
	
	function set_sort($sort)
	{
		$this->set_default_property(self :: PROPERTY_SORT, $sort);
	}
	
	function get_parent()
	{
		return $this->get_default_property(self :: PROPERTY_PARENT);
	}
	
	function set_parent($parent)
	{
		$this->set_default_property(self :: PROPERTY_PARENT, $parent);
	}
	
	function get_left_value()
	{
		return $this->get_default_property(self :: PROPERTY_LEFT_VALUE);
	}
		
	function set_left_value($left_value)
	{
		$this->set_default_property(self :: PROPERTY_LEFT_VALUE, $left_value);
	}
	
	function get_right_value()
	{
		return $this->get_default_property(self :: PROPERTY_RIGHT_VALUE);
	}
		
	function set_right_value($right_value)
	{
		$this->set_default_property(self :: PROPERTY_RIGHT_VALUE, $right_value);
	}
	
	/**
	 * Get all of the group's parents 
	 */
	function get_parents($include_self = true)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$parent_conditions = array();
		if ($include_self)
		{
			$parent_conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN_OR_EQUAL, $this->get_left_value());
			$parent_conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $this->get_right_value());
		}
		else
		{
			$parent_conditions[] = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: LESS_THAN, $this->get_left_value());
			$parent_conditions[] = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_right_value());
		}
		
		$parent_condition = new AndCondition($parent_conditions);
		$order = array(Group :: PROPERTY_LEFT_VALUE);
		$order_direction = array(SORT_DESC);
			
		return $rdm->retrieve_groups($parent_condition, null, null, $order, $order_direction);
	}
	
	function is_child_of($parent_id)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$parent = $gdm->retrieve_group($parent_id);
		// TODO: What if $parent is invalid ? Return error

        // Check if the left and right value of the child are within the
        // left and right value of the parent, if so it is a child
        if ($parent->get_left_value() < $this->get_left_value() && $parent->get_right_value() > $this->get_right_value())
        {
            return true;
        }

        return false;
	}
	
	/**
	 * Get the groups on the same level with the same parent
	 */
	function get_siblings($include_self = true)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$siblings_conditions = array();
		$siblings_conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_parent());
		
		if (!$include_self)
		{
			$siblings_conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $this->get_id()));
		}
		
		$siblings_condition = new AndCondition($siblings_conditions);
			
		return $gdm->retrieve_groups($siblings_condition);
	}
	
	function has_siblings()
	{
		$gdm = GroupDataManager :: get_instance();
		
		$siblings_conditions = array();
		$siblings_conditions[] = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_parent());
		$siblings_conditions[] = new NotCondition(new EqualityCondition(Group :: PROPERTY_ID, $this->get_id()));
		
		$siblings_condition = new AndCondition($siblings_conditions);
			
		return ($gdm->count_groups($siblings_condition) > 0);
	}
	
	/**
	 * Get the group's children
	 */
	function get_children($recursive = true)
	{
		$gdm = GroupDataManager :: get_instance();
		
		if ($recursive)
		{
			$children_conditions = array();
			$children_conditions = new InequalityCondition(Group :: PROPERTY_LEFT_VALUE, InequalityCondition :: GREATER_THAN, $this->get_left_value());
			$children_conditions = new InequalityCondition(Group :: PROPERTY_RIGHT_VALUE, InequalityCondition :: LESS_THAN, $this->get_right_value());
			$children_condition = new AndCondition($children_conditions);
		}
		else
		{
			$children_condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_id());
		}
		
		return $gdm->retrieve_groups($children_condition);
	}
	
	function has_children()
	{
		$gdm = GroupDataManager :: get_instance();
		$children_condition = new EqualityCondition(Location :: PROPERTY_PARENT, $this->get_id());
		return ($gdm->count_groups($children_condition) > 0);
	}
	
	function move($new_parent_id, $new_previous_id = 0)
	{
		$gdm = GroupDataManager :: get_instance();
		
		if (!$gdm->move_location($this, $new_parent_id, $new_previous_id))
		{
			return false;
		}
		
		return true;
	}
	
	/**
	 * Instructs the Datamanager to delete this group.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		$gdm = GroupDataManager :: get_instance();
		
		// Delete the actual location
		if (!$gdm->delete_group($this))
		{
			return false;
		}
		
		// Update left and right values
		if (!$gdm->delete_nested_values($this))
		{
        	// TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
        	return false;
		}
	}
	
	function truncate()
	{
		return GroupDataManager :: get_instance()->truncate_group($this);
	}
	
	function create($previous_id = 0)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$parent_id = $this->get_parent();
		
        $previous_visited = 0;

        if ($parent_id || $previous_id)
        {
            if ($previous_id)
            {
            	$node = $gdm->retrieve_group($previous_id);
            	$parent_id = $node->get_parent();
            	
            	// TODO: If $node is invalid, what then ?
            }
            else
            {
            	$node = $gdm->retrieve_group($parent_id);
            }
            
            // Set the new location's parent id
            $this->set_parent($parent_id);

			// TODO: If $node is invalid, what then ?

            // get the "visited"-value where to add the new element behind
            // if $previous_id is given, we need to use the right-value
            // if only the $parent_id is given we need to use the left-value
            $previous_visited = $previous_id ? $node->get_right_value() : $node->get_left_value();
            
            // Correct the left and right values wherever necessary.
            if (!$gdm->add_nested_values($this, $previous_visited, 1))
            {
            	// TODO: Some kind of general error handling framework would be nice: PEAR-ERROR maybe ?
            	return false;
            }
        }
        
        // Left and right values have been shifted so now we
        // want to really add the location itself, but first
        // we have to set it's left and right value.
        $this->set_left_value($previous_visited + 1);
        $this->set_right_value($previous_visited + 2);
        $this->set_id($gdm->get_next_group_id());
        if (!$gdm->create_group($this))
        {
        	return false;
        }
        
        return true;
	}
	
	function update() 
	{
		$gdm = GroupDataManager :: get_instance();
		
		$condition = new EqualityCondition(self :: PROPERTY_NAME, $this->get_name());
		$groups = $gdm->count_groups($condition);
		if($groups > 1)
			return false;
			
		$success = $gdm->update_group($this);
		if (!$success)
		{
			return false;
		}

		return true;	
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	function get_roles()
	{
		$gdm = GroupDataManager :: get_instance();
		$condition = new EqualityCondition(GroupRole :: PROPERTY_GROUP_ID, $this->get_id());
		
		return $gdm->retrieve_group_roles($condition);
	}
	
	function add_role_link($role_id)
	{
		$gdm = GroupDataManager :: get_instance();
		return $gdm->add_role_link($this, $role_id);
	}
	
	function delete_role_link($role_id)
	{
		$gdm = GroupDataManager :: get_instance();
		return $gdm->delete_role_link($this, $role_id);
	}
	
	function update_role_links($roles)
	{
		$gdm = GroupDataManager :: get_instance();
		return $gdm->update_role_links($this, $roles);
	}
	
	function get_users($include_subgroups = false, $recursive_subgroups = false)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$groups = array();
		$groups[] = $this->get_id();
		
		if ($include_subgroups)
		{
			$subgroups =  $this->get_subgroups($recursive_subgroups);
			
			foreach($subgroups as $subgroup)
			{
				$groups[] = $subgroup->get_id();
			}
		}
		
		$condition = new InCondition(GroupRelUser :: PROPERTY_GROUP_ID, $groups);
		$group_rel_users = $gdm->retrieve_group_rel_users($condition);
		$users = array();
		
		while ($group_rel_user = $group_rel_users->next_result())
		{
			$user_id = $group_rel_user->get_user_id();
			if (!in_array($user_id, $users))
			{
				$users[] = $user_id;
			}
		}
		
		return $users;
	}
	
	function count_users($include_subgroups = false, $recursive_subgroups = false)
	{
		$users = $this->get_users($include_subgroups, $recursive_subgroups);
		
		return count($users);
	}
	
	function get_subgroups($recursive = false)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$condition = new EqualityCondition(self :: PROPERTY_PARENT, $this->get_id());
		$groups = $gdm->retrieve_groups($condition);
		
		$subgroups = array();
		
		while ($group = $groups->next_result())
		{
			$subgroups[$group->get_id()] = $group;
			
			if ($recursive)
			{
				$subgroups += $group->get_subgroups($recursive);
			}
		}
		
		return $subgroups;
	}
	
	function count_subgroups($recursive = false)
	{
		$gdm = GroupDataManager :: get_instance();
		
		$condition = new EqualityCondition(Group :: PROPERTY_PARENT,$this->get_id()); 
		$count = $gdm->count_groups($condition);
		
		if($recursive)
		{
			$subgroups = $this->get_subgroups($recursive);
			
			foreach($subgroups as $subgroup)
			{
				$count += $subgroup->count_subgroups();
			}
		}
		
		return $count;
	}
}
?>