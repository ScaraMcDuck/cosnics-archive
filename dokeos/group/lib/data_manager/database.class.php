<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_group_result_set.class.php';
require_once dirname(__FILE__).'/database/database_group_rel_user_result_set.class.php';
require_once dirname(__FILE__).'/../group_data_manager.class.php';
require_once dirname(__FILE__).'/../group.class.php';
require_once dirname(__FILE__).'/../group_rel_user.class.php';
require_once dirname(__FILE__).'/../group_role.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Sven Vanpoucke
==============================================================================
 */

class DatabaseGroupDataManager extends GroupDataManager
{
	private $database;
	
	function initialize()
	{
		$this->database = new Database(array('group' => 'cg', 'group_rel_user' => 'cgru', 'group_role' => 'gr'));
		$this->database->set_prefix('group_');
	}
	
	function update_group($group)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_ID, $group->get_id());
		return $this->database->update($group, $condition);
	}
	
	function get_next_group_id()
	{
		$id = $this->database->get_next_id(Group :: get_table_name());
		return $id;
	}
	
	function delete_group($group)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_ID, $group->get_id());
		$bool = $this->database->delete($group->get_table_name(), $condition);
		
		$condition_subgroups = new EqualityCondition(Group :: PROPERTY_PARENT, $group->get_id());
		$groups = $this->retrieve_groups($condition_subgroups);
		while($gr = $groups->next_result())
		{
			$bool = $bool & $this->delete_group($gr);
		}
		
		$this->truncate_group($group);
		
		return $bool;
		
	}
	
	function truncate_group($group)
	{
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
		return $this->database->delete(GroupRelUser :: get_table_name(), $condition);
	}
	
	function delete_group_rel_user($groupreluser)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $groupreluser->get_group_id());
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $groupreluser->get_user_id());
		$condition = new AndCondition($conditions);
		
		return $this->database->delete($groupreluser->get_table_name(), $condition);
	}
	
	function create_group($group)
	{
		return $this->database->create($group);
	}
	
	function create_group_rel_user($groupreluser)
	{
		return $this->database->create($groupreluser);
	}
	
	function count_groups($condition = null)
	{
		return $this->database->count_objects(Group :: get_table_name(), $condition);
	}
	
	function count_group_rel_users($condition = null)
	{
		return $this->database->count_objects(GroupRelUser :: get_table_name(), $condition);
	}
	
	function retrieve_groups($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(Group :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_group_rel_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(GroupRelUser :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_group_rel_user($user_id, $group_id)
	{
		$conditions = array();		
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id);
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		
		return $this->database->retrieve_object(GroupRelUser :: get_table_name(), $condition);
	}
	
	function retrieve_user_groups($user_id)
	{
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id);
		return $this->database->retrieve_objects(GroupRelUser :: get_table_name(), $condition);
	}
	
	function retrieve_group($id)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Group :: get_table_name(), $condition);
	}
	
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}
	
	function retrieve_group_roles($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{		
		return $this->database->retrieve_objects(GroupRole :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function delete_group_roles($condition)
	{
		return $this->database->delete(GroupRole :: get_table_name(), $condition);
	}
}
?>