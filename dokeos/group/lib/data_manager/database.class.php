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
		$this->database = new Database(array('group' => 'cg', 'group_rel_user' => 'cgru'));
		$this->database->set_prefix('group_');
	}
	
	function update_classgroup($classgroup)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_ID, $classgroup->get_id());
		return $this->database->update($classgroup, $condition);
	}
	
	function get_next_classgroup_id()
	{
		$id = $this->database->get_next_id(Group :: get_table_name());
		return $id;
	}
	
	function delete_classgroup($classgroup)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_ID, $classgroup->get_id());
		return $this->database->delete($classgroup->get_table_name(), $condition);
	}
	
	function truncate_classgroup($classgroup)
	{
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $classgroup->get_id());
		return $this->database->delete(GroupRelUser :: get_table_name(), $condition);
	}
	
	function delete_classgroup_rel_user($classgroupreluser)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $classgroupreluser->get_classgroup_id());
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $classgroupreluser->get_user_id());
		$condition = new AndCondition($conditions);
		
		return $this->database->delete($classgroupreluser->get_table_name(), $condition);
	}
	
	function create_classgroup($classgroup)
	{
		return $this->database->create($classgroup);
	}
	
	function create_classgroup_rel_user($classgroupreluser)
	{
		return $this->database->create($classgroupreluser);
	}
	
	function count_classgroups($condition = null)
	{
		return $this->database->count_objects(Group :: get_table_name(), $condition);
	}
	
	function count_classgroup_rel_users($condition = null)
	{
		return $this->database->count_objects(GroupRelUser :: get_table_name(), $condition);
	}
	
	function retrieve_classgroups($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(Group :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_classgroup_rel_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(GroupRelUser :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_classgroup_rel_user($user_id, $group_id)
	{
		$conditions = array();		
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_USER_ID, $user_id);
		$conditions[] = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		
		return $this->database->retrieve_object(GroupRelUser :: get_table_name(), $condition);
	}
	
	function retrieve_classgroup($id)
	{
		$condition = new EqualityCondition(Group :: PROPERTY_ID, $id);
		return $this->database->retrieve_object(Group :: get_table_name(), $condition);
	}
	
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}
}
?>