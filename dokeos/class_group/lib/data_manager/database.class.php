<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_class_group_result_set.class.php';
require_once dirname(__FILE__).'/database/database_class_group_rel_user_result_set.class.php';
require_once dirname(__FILE__).'/../class_group_data_manager.class.php';
require_once dirname(__FILE__).'/../class_group.class.php';
require_once dirname(__FILE__).'/../class_group_rel_user.class.php';
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

class DatabaseClassGroupDataManager extends ClassGroupDataManager
{
	private $db;
	
	function initialize()
	{
		$this->db = new Database(array('class_group' => 'cg', 'class_group_rel_user' => 'cgru'));
		$this->db->set_prefix('class_group_');
	}
	
	function update_classgroup($classgroup)
	{
		$condition = new EqualityCondition(ClassGroup :: PROPERTY_ID, $classgroup->get_id());
		return $this->db->update($classgroup, 'class_group', $condition);
	}
	
	function get_next_classgroup_id()
	{
		$id = $this->db->get_next_id('class_group');
		return $id;
	}
	
	function delete_classgroup($classgroup)
	{
		$condition = new EqualityCondition(ClassGroup :: PROPERTY_ID, $classgroup->get_id());
		return $this->db->delete('class_group', $condition);
	}
	
	function truncate_classgroup($classgroup)
	{
		$condition = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $classgroup->get_id());
		return $this->db->delete('class_group_rel_user', $condition);
	}
	
	function delete_classgroup_rel_user($classgroupreluser)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $classgroupreluser->get_classgroup_id());
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_USER_ID, $classgroupreluser->get_user_id());
		$condition = new AndCondition($conditions);
		
		return $this->db->delete('class_group_rel_user', $condition);
	}
	
	function create_classgroup($classgroup)
	{
		return $this->db->create($classgroup, 'class_group');
	}
	
	function create_classgroup_rel_user($classgroupreluser)
	{
		return $this->db->create($classgroupreluser, 'class_group_rel_user');
	}
	
	function count_classgroups($condition = null)
	{
		return $this->db->count_objects('class_group', $condition);
	}
	
	function count_classgroup_rel_users($condition = null)
	{
		return $this->db->count_objects('class_group_rel_user', $condition);
	}
	
	function retrieve_classgroups($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->db->retrieve_objects('class_group', 'ClassGroup', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_classgroup_rel_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->db->retrieve_objects('class_group_rel_user', 'ClassGroupRelUser', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_classgroup_rel_user($user_id, $group_id)
	{
		$conditions = array();		
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_USER_ID, $user_id);
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		
		$group_rel_users = $this->db->retrieve_objects('class_group_rel_user', 'ClassGroupRelUser', $condition);
		return $group_rel_users->next_result();
	}
	
	function retrieve_classgroup($id)
	{
		$condition = new EqualityCondition(ClassGroup :: PROPERTY_ID, $id);
		$groups = $this->db->retrieve_objects('class_group', 'ClassGroup', $condition);
		return $groups->next_result();
	}
	
	function create_storage_unit($name,$properties,$indexes)
	{
		return $this->db->create_storage_unit($name,$properties,$indexes);
	}
}
?>