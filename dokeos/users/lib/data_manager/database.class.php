<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/databaseuserresultset.class.php';
require_once dirname(__FILE__).'/../users_data_manager.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../user_quota.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path() . 'database/database.class.php';
require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseUsersDataManager extends UsersDataManager
{
	private $database;
	
	/**
	 * Initializes the connection
	 */
	function initialize()
	{
		$this->database = new Database(array('user' => 'u','user_quota' => 'uq'));
		$this->database->set_prefix('user_');
	}
	
	function update_user($user)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_id());
		return $this->database->update($user, $condition);
	}
	
	function update_user_quota($user_quota)
	{
		$conditions = array();		
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_USER_ID, $user_quota->get_user_id());
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_LEARNING_OBJECT_TYPE, $user_quota->get_learning_object_type());
		$condition = new AndCondition($conditions);
		
		return $this->database->update($user_quota, $condition);
	}
	
	function get_next_user_id()
	{
		return $this->database->get_next_id(User :: get_table_name());
	}
	
	function delete_user($user)
	{
		// @Todo: review the user's objects on deletion 
		// (currently: when the user is deleted, the user's objects remain, and refer to an invalid user)
		$condition = new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_id());
		return $this->db->delete('user', $condition);
	}
	
	function delete_all_users()
	{
		$users = $this->retrieve_users()->as_array();
		foreach($users as $index => $user)
		{
			$this->delete_user($user);
		}
	}
	
	function create_user($user)
	{
		return $this->database->create($user);
	}
	
	// Inherited.
	function create_storage_unit($name, $properties, $indexes)
	{
		return $this->database->create_storage_unit($name, $properties, $indexes);
	}


	function retrieve_user($id)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USER_ID, $id);
		return $this->database->retrieve_object(User :: get_table_name(), $condition);
	}

	function retrieve_user_by_username($username)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME, $username);
		return $this->database->retrieve_object(User :: get_table_name(), $condition);
	}
	
	function retrieve_users_by_email($email)
	{
		$condition = new EqualityCondition(User :: PROPERTY_EMAIL, $email);
		$users = $this->database->retrieve_objects(User :: get_table_name(), $condition);
		return $users->next_result();
	}

	/*
	//Inherited.
	function is_username_available($username, $user_id = null)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
		if($user_id)
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
			$conditions[] = new EqualityCondition(User :: PROPERTY_USER_ID,$user_id);
			$condition = new AndCondition($conditions);
		}
<<<<<<< .mine
		return !($this->db->count_objects('user',$condition) == 1);
=======
		return $this->database->count_objects(User :: get_table_name(), $condition) < 1;
>>>>>>> .r15882
	}
	//*/
	///*
	function is_username_available($username, $user_id = null)
	{
		$params = array();
		$query = 'SELECT username FROM '.$this->database->escape_table_name('user').' WHERE '.$this->database->escape_column_name(User :: PROPERTY_USERNAME).'=?';
		$params[] = $username;
		if ($user_id)
		{
			$query .=  ' AND '.$this->database->escape_column_name(User :: PROPERTY_USER_ID).' !=?';
			$params[] = $user_id;
		}
		$statement = $this->database->get_connection()->prepare($query);
		$result = $statement->execute($params);
		if ($result->numRows() == 1)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	//*/

	function count_users($condition = null)
	{
		return $this->database->count_objects(User :: get_table_name(), $condition);
	}

	function retrieve_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		return $this->database->retrieve_objects(User :: get_table_name(), $condition, $offset, $maxObjects, $orderBy, $orderDir);
	}

	//Inherited.
	function retrieve_version_type_quota($user, $type)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(User :: PROPERTY_USER_ID,$user->get_id());
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_LEARNING_OBJECT_TYPE,$type);
		$condition = new AndCondition($conditions);
		
		$user_quotum = $this->database->retrieve_object(UserQuota :: get_table_name(), $condition);
		
		return $user_quotum->get_user_quota();
		
		/*
		$query = 'SELECT * FROM '.$this->escape_table_name('user_quota').' WHERE '.$this->escape_column_name(User :: PROPERTY_USER_ID).'=? AND '.$this->escape_column_name('learning_object_type').'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array($user->get_id(), $type));

		if ($res->numRows() >= 1)
		{
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();
			return $record['user_quota'];
		}
		else
		{
			return null;
		}
		*/
	}
}
?>