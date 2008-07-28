<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_class_group_result_set.class.php';
require_once dirname(__FILE__).'/database/database_class_group_rel_user_result_set.class.php';
require_once dirname(__FILE__).'/data_manager_functions.class.php';
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

class DatabaseClassGroupDataManager extends Database implements DataManagerFunctions
{
	const ALIAS_CLASSGROUP_TABLE = 'g';
	private $tablenames = array('class_group' => 'cg', 'class_group_rel_user' => 'cgru'); 
	
	function initialize()
	{
		$this->set_prefix('class_group_');
		parent :: initialize();
	}
	
	function get_alias($table_name)
	{
		return $this->tablenames[$table_name];
	}
	
	function update_classgroup($classgroup)
	{
		$condition = new EqualityCondition(ClassGroup :: PROPERTY_ID, $classgroup->get_id());
		return $this->update($classgroup, 'class_group', $condition);
	}
	
	function get_next_classgroup_id()
	{
		$id = $this->get_next_id('class_group');
		return $id;
	}
	
	function delete_classgroup($classgroup)
	{
		$condition = new EqualityCondition(ClassGroup :: PROPERTY_ID, $classgroup->get_id());
		return $this->delete('class_group', $condition);
	}
	
	function truncate_classgroup($classgroup)
	{
		$condition = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $classgroup->get_id());
		return $this->delete('class_group_rel_user', $condition);
	}
	
	function delete_classgroup_rel_user($classgroupreluser)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $classgroupreluser->get_classgroup_id());
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_USER_ID, $classgroupreluser->get_user_id());
		$condition = new AndCondition($conditions);
		
		return $this->delete('class_group_rel_user', $condition);
	}
	
	function create_classgroup($classgroup)
	{
		return $this->create($classgroup, 'class_group');
	}
	
	function create_classgroup_rel_user($classgroupreluser)
	{
		return $this->create($classgroupreluser, 'class_group_rel_user');
	}
	
	function count_classgroups($condition = null)
	{
		return $this->count_objects('class_group', $condition);
	}
	
	function count_classgroup_rel_users($condition = null)
	{
		return $this->count_objects('class_group_rel_user', $condition);
	}
	
	function retrieve_classgroups($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->retrieve_objects('class_group', 'ClassGroup', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_classgroup_rel_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->retrieve_objects('class_group_rel_user', 'ClassGroupRelUser', $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}
	
	function retrieve_classgroup_rel_user($user_id, $group_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('class_group_rel_user') . ' AS ' . $this->tablenames['class_group_rel_user'];
		
		$params = array ();
		$conditions = array();		
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_USER_ID, $user_id);
		$conditions[] = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $group_id);
		$condition = new AndCondition($conditions);
		
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $this->tablenames['class_group_rel_user']);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$this->get_connection()->setLimit(1);
		$statement = $this->get_connection()->prepare($query);
		$res = $statement->execute($params);
		
		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();
			return self :: record_to_classobject($record, 'ClassGroupRelUser');
		}
		else
		{
			return null;
		}
	}
	
	function retrieve_classgroup($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('class_group').' WHERE '.$this->escape_column_name(ClassGroup :: PROPERTY_ID).'=?';
		$this->get_connection()->setLimit(1);
		$statement = $this->get_connection()->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_classobject($record, 'ClassGroup');
	}
}
?>