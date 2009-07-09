<?php
/**
 * @package users
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/../user_data_manager.class.php';
require_once dirname(__FILE__).'/../user.class.php';
require_once dirname(__FILE__).'/../user_quota.class.php';
require_once dirname(__FILE__).'/../user_role.class.php';
require_once dirname(__FILE__).'/../buddy_list_item.class.php';
require_once dirname(__FILE__).'/../buddy_list_category.class.php';
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

class DatabaseUserDataManager extends UserDataManager
{
	const ALIAS_USER = 'u';

	private $database;

	/**
	 * Initializes the connection
	 */
	function initialize()
	{
		$this->database = new Database(array(User :: get_table_name() => self :: ALIAS_USER,'user_quota' => 'uq', 'user_role' => 'ur', 'buddy_list_category' => 'blc', 'buddy_list_item' => 'bli'));
		$this->database->set_prefix('user_');
	}

    function get_database()
    {
        return $this->database;
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

	function create_user_quota($user_quota)
	{
		return $this->database->create($user_quota);
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
		return $this->database->delete($user->get_table_name(), $condition);
	}

	function delete_user_roles($condition)
	{
		return $this->database->delete(UserRole :: get_table_name(), $condition);
	}

	function delete_user_role($user_role)
	{
	    $conditions  = array();
	    $conditions[] = new EqualityCondition(UserRole :: PROPERTY_USER_ID, $user_role->get_user_id());
	    $conditions[] = new EqualityCondition(UserRole :: PROPERTY_ROLE_ID, $user_role->get_role_id());
	    $condition   = new AndCondition($conditions);

		return $this->database->delete($user_role->get_table_name(), $condition);
	}

	function create_user_role($user_role){
		return $this->database->create($user_role);
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
		$this->database->create($user);

		// Create the user's root category for the repository
		//RepositoryDataManager :: get_instance()->create_root_category($user->get_id());

		return true;
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

	function retrieve_user_by_external_uid($external_uid)
	{
		$condition = new EqualityCondition(User :: PROPERTY_EXTERNAL_UID, $external_uid);
		return $this->database->retrieve_object(User :: get_table_name(), $condition);
	}

	function retrieve_users_by_email($email)
	{
		$condition = new EqualityCondition(User :: PROPERTY_EMAIL, $email);
		$users = $this->database->retrieve_objects(User :: get_table_name(), $condition);
		return $users->next_result();
	}

	//Inherited.
	function is_username_available($username, $user_id = null)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
		if($user_id)
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
			$conditions = new EqualityCondition(User :: PROPERTY_USER_ID, $user_id);
			$condition = new AndCondition($conditions);
		}
		return !($this->database->count_objects(User :: get_table_name(), $condition) == 1);
	}

	function retrieve_user_info($username)
	{
		$condition = new EqualityCondition(User :: PROPERTY_USERNAME,$username);
		return $this->retrieve_users($condition)->next_result();
	}

	function count_users($condition = null)
	{
		return $this->database->count_objects(User :: get_table_name(), $condition);
	}

	function retrieve_users($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(User :: get_table_name(), $condition, $offset, $max_objects, $order_by, $order_dir);
	}

	//Inherited.
	function retrieve_version_type_quota($user, $type)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(User :: PROPERTY_USER_ID, $user->get_id());
		$conditions[] = new EqualityCondition(UserQuota :: PROPERTY_LEARNING_OBJECT_TYPE, $type);
		$condition = new AndCondition($conditions);

		$version_type_quota_set = $this->database->count_objects(UserQuota :: get_table_name(), $condition) > 0;

		if ($version_type_quota_set)
		{
			$user_quotum = $this->database->retrieve_object(UserQuota :: get_table_name(), $condition);
			return $user_quotum->get_user_quota();
		}
		else
		{
			return null;
		}

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

	function retrieve_user_roles($condition = null, $offset = null, $max_objects = null, $order_by = null, $order_dir = null)
	{
		return $this->database->retrieve_objects(UserRole :: get_table_name(), $condition);
	}

	function add_role_link($user, $role_id)
	{
		$props = array();
		$props[UserRole :: PROPERTY_USER_ID] = $user->get_id();
		$props[UserRole :: PROPERTY_ROLE_ID] = $role_id;
		$this->database->get_connection()->loadModule('Extended');
		return $this->database->get_connection()->extended->autoExecute($this->database->get_table_name(UserRole :: get_table_name()), $props, MDB2_AUTOQUERY_INSERT);
	}

	function delete_role_link($user, $role_id)
	{
		$conditions = array();
		$conditions = new EqualityCondition(UserRole :: PROPERTY_USER_ID, $user->get_id());
		$conditions = new EqualityCondition(UserRole :: PROPERTY_ROLE_ID, $role_id);
		$condition = new AndCondition($conditions);

		return $this->database->delete(UserRole :: get_table_name(), $condition);
	}

	function update_role_links($user, $roles)
	{
		// Delete the no longer existing links
		$conditions = array();
		$conditions = new NotCondition(new InCondition(UserRole :: PROPERTY_ROLE_ID, $roles));
		$conditions = new EqualityCondition(UserRole :: PROPERTY_USER_ID, $user->get_id());
		$condition = new AndCondition($conditions);

		$success = $this->database->delete(UserRole :: get_table_name(), $condition);
		if (!$success)
		{
			return false;
		}

		// Get the group's roles
		$condition = new EqualityCondition(UserRole :: PROPERTY_USER_ID, $user->get_id());
		$user_roles = $this->retrieve_user_roles($condition);
		$existing_roles = array();

		while($user_role = $user_roles->next_result())
		{
			$existing_roles[] = $user_role->get_role_id();
		}

		// Add the new links
		foreach ($roles as $role)
		{
			if (!in_array($role, $existing_roles))
			{
				if (!$this->add_role_link($user, $role))
				{
					return false;
				}
			}
		}

		return true;
	}

	function get_next_buddy_list_category_id()
	{
		return $this->database->get_next_id(BuddyListCategory :: get_table_name());
	}

	function create_buddy_list_category($buddy_list_category)
	{
		return $this->database->create($buddy_list_category);
	}

	function update_buddy_list_category($buddy_list_category)
	{
		$condition = new EqualityCondition(BuddyListCategory :: PROPERTY_ID, $buddy_list_category->get_id());
		return $this->database->update($buddy_list_category, $condition);
	}

	function delete_buddy_list_category($buddy_list_category)
	{
		$condition = new EqualityCondition(BuddyListCategory :: PROPERTY_ID, $buddy_list_category->get_id());
		$succes = $this->database->delete(BuddyListCategory :: get_table_name(), $condition);

		$query = 'UPDATE '.$this->database->escape_table_name('buddy_list_item').' SET '.
				 $this->database->escape_column_name(BuddyListItem :: PROPERTY_CATEGORY_ID).'=0 WHERE'.
				 $this->database->escape_column_name(BuddyListItem :: PROPERTY_CATEGORY_ID).'=?;';
		$statement = $this->database->get_connection()->prepare($query);
		$statement->execute(array($buddy_list_category->get_id()));

		return $succes;
	}

	function retrieve_buddy_list_categories($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->database->retrieve_objects(BuddyListCategory :: get_table_name(), $condition, $offset, $count, $order_property, $order_direction);
	}

	function get_next_buddy_list_item_id()
	{
		return $this->database->get_next_id(BuddyListItem :: get_table_name());
	}

	function create_buddy_list_item($buddy_list_item)
	{
		return $this->database->create($buddy_list_item);
	}

	function update_buddy_list_item($buddy_list_item)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $buddy_list_item->get_user_id());
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $buddy_list_item->get_buddy_id());
		$condition = new AndCondition($conditions);

		return $this->database->update($buddy_list_item, $condition);
	}

	function delete_buddy_list_item($buddy_list_item)
	{
		$conditions = array();
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_USER_ID, $buddy_list_item->get_user_id());
		$conditions[] = new EqualityCondition(BuddyListItem :: PROPERTY_BUDDY_ID, $buddy_list_item->get_buddy_id());
		$condition = new AndCondition($conditions);

		return $this->database->delete(BuddyListItem :: get_table_name(), $condition);
	}

	function retrieve_buddy_list_items($condition = null, $offset = null, $count = null, $order_property = null, $order_direction = null)
	{
		return $this->database->retrieve_objects(BuddyListItem :: get_table_name(), $condition, $offset, $count, $order_property, $order_direction);
	}
}
?>