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
	const ALIAS_USER_TABLE = 'u';

	/**
	 * The database connection.
	 */
	private $connection;

	/**
	 * The table name prefix, if any.
	 */
	private $prefix;

	/**
	 * Initializes the connection
	 */
	function initialize()
	{
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
		$this->prefix = 'user_';
		$this->connection->query('SET NAMES utf8');
	}

	function debug()
	{
		$args = func_get_args();
		// Do something with the arguments
		if($args[1] == 'query')
		{
			//echo '<pre>';
		 	//echo($args[2]);
		 	//echo '</pre>';
		}
	}

	/**
	 * Escapes a column name in accordance with the database type.
	 * @param string $name The column name.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The escaped column name.
	 */
	function escape_column_name($name, $prefix_user_object_properties = false)
	{
		// Check whether the name contains a seperator, avoids notices.
		$contains_table_name = strpos($name, '.');
		if ($contains_table_name === false)
		{
			$table = $name;
			$column = null;
		}
		else
		{
			list($table, $column) = explode('.', $name, 2);
		}
		
		$prefix = '';
		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
		elseif ($prefix_user_object_properties && self :: is_user_column($name))
		{
			$prefix = self :: ALIAS_USER_TABLE.'.';
		}
		return $prefix . $this->connection->quoteIdentifier($name);
	}

	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table identifier.
	 * @return string The escaped table name.
	 */
	function escape_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		$database_name = $this->connection->quoteIdentifier($dsn['database']);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}

	/**
	 * Checks whether the given name is a user column
	 * @param String $name The column name
	 */
	private static function is_user_column($name)
	{
		return User :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
	}

	/**
	 * Checks whether the given column name is the name of a column that
	 * contains a date value, and hence should be formatted as such.
	 * @param string $name The column name.
	 * @return boolean True if the column is a date column, false otherwise.
	 */
	static function is_date_column($name)
	{
		return ($name == LearningObject :: PROPERTY_CREATION_DATE || $name == LearningObject :: PROPERTY_MODIFICATION_DATE);
	}

	// Inherited.
	function update_user($user)
	{
		$where = $this->escape_column_name(User :: PROPERTY_USER_ID).'='.$user->get_id();
		$props = array();
		foreach ($user->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('user'), $props, MDB2_AUTOQUERY_UPDATE, $where);

		return true;
	}

	//Inherited.
	function update_user_quota($user_quota)
	{
		$where = $this->escape_column_name(UserQuota :: PROPERTY_USER_ID).'='.$user_quota->get_user_id(). ' AND '. $this->escape_column_name(UserQuota :: PROPERTY_LEARNING_OBJECT_TYPE).'="'.$user_quota->get_learning_object_type(). '"';
		$props = array();
		foreach ($user_quota->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[UserQuota :: PROPERTY_USER_ID] = $user_quota->get_user_id();
		$this->connection->loadModule('Extended');
		$quota_type = $this->retrieve_version_type_quota($this->retrieve_user($user_quota->get_user_id()), $user_quota->get_learning_object_type());
		if ($quota_type)
		{
			$this->connection->extended->autoExecute($this->get_table_name('user_quota'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		}
		else
		{
			$this->connection->extended->autoExecute($this->get_table_name('user_quota'), $props, MDB2_AUTOQUERY_INSERT);
		}

	return true;
	}

	// Inherited.
	function get_next_user_id()
	{
		$id = $this->connection->nextID($this->get_table_name('user'));
		return $id;
	}

	// Inherited.
	function delete_user($user)
	{
		if(!$this->user_deletion_allowed($user))
		{
			return false;
		}

		RepositoryDataManager :: get_instance()->delete_learning_object_by_user($user->get_id());
		// Delete the user from the database
		$query = 'DELETE FROM '.$this->escape_table_name('user').' WHERE '.$this->escape_column_name('user_id').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($user->get_id());

		return true;
	}

	// Inherited.
	function delete_all_users()
	{
		$users = $this->retrieve_users()->as_array();
		foreach($users as $index => $user)
		{
			$this->delete_user($user);
		}
	}

	// Inherited.
	function create_user($user)
	{
		$props = array();
		foreach ($user->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(User :: PROPERTY_USER_ID)] = $user->get_id();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('user'), $props, MDB2_AUTOQUERY_INSERT);

		// Create the user's root category for the repository
		RepositoryDataManager :: get_instance()->create_root_category($user->get_id());
		return true;
	}

	// Inherited.
	function create_storage_unit($name,$properties,$indexes)
	{
		$name = $this->get_table_name($name);
		$this->connection->loadModule('Manager');
		$manager = $this->connection->manager;
		// If table allready exists -> drop it
		// @todo This should change: no automatic table drop but warning to user
		$tables = $manager->listTables();
		if( in_array($name,$tables))
		{
			$manager->dropTable($name);
		}
		$options['charset'] = 'utf8';
		$options['collate'] = 'utf8_unicode_ci';
		if (!MDB2 :: isError($manager->createTable($name,$properties,$options)))
		{
			foreach($indexes as $index_name => $index_info)
			{
				if($index_info['type'] == 'primary')
				{
					$index_info['primary'] = 1;
					if (MDB2 :: isError($manager->createConstraint($name,$index_name,$index_info)))
					{
						return false;
					}
				}
				else
				{
					if (MDB2 :: isError($manager->createIndex($name,$index_name,$index_info)))
					{
						return false;
					}
				}
			}
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Expands a table identifier to the real table name. Currently, this
	 * method prefixes the given table name with the user-defined prefix, if
	 * any.
	 * @param string $name The table identifier.
	 * @return string The actual table name.
	 */
	function get_table_name($name)
	{
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
	}

	//Inherited.
	function retrieve_user($id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('user').' AS '.self :: ALIAS_USER_TABLE.' WHERE '.$this->escape_column_name(User :: PROPERTY_USER_ID).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_user($record);
	}

	//Inherited.
	function retrieve_user_by_username($username)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('user').' AS '.self :: ALIAS_USER_TABLE.' WHERE '.$this->escape_column_name(User :: PROPERTY_USERNAME).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($username);
		if($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$res->free();
			return self :: record_to_user($record);
		}
		return null;
	}
	//Inherited.
	function retrieve_users_by_email($email)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('user').' AS '.self :: ALIAS_USER_TABLE.' WHERE '.$this->escape_column_name(User :: PROPERTY_EMAIL).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($email);
		$users = array();
		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$users[] = self :: record_to_user($record);
		}
		$res->free();
		return $users;
	}

	/**
	 * Parses a database record fetched as an associative array into an user.
	 * @param array $record The associative array.
	 * @return LearningObject The learning object.
	 */
	function record_to_user($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (User :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new User($record[User :: PROPERTY_USER_ID], $defaultProp);
	}

	//Inherited.
	function is_username_available($username, $user_id = null)
	{
		$params = array();
		$query = 'SELECT username FROM '.$this->escape_table_name('user').' WHERE '.$this->escape_column_name(User :: PROPERTY_USERNAME).'=?';
		$params[] = $username;
		if ($user_id)
		{
			$query .=  ' AND '.$this->escape_column_name(User :: PROPERTY_USER_ID).' !=?';
			$params[] = $user_id;
		}
		$statement = $this->connection->prepare($query);
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

	//Inherited
	function count_users($condition = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name(User :: PROPERTY_USER_ID).') FROM '.$this->escape_table_name('user').' AS '. self :: ALIAS_USER_TABLE;

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		return $record[0];
	}

	//Inherited.
	function retrieve_users($condition = null, $offset = null, $maxObjects = null, $orderBy = null, $orderDir = null)
	{
		$query = 'SELECT * FROM '. $this->escape_table_name('user'). ' AS '. self :: ALIAS_USER_TABLE;

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = User :: PROPERTY_LASTNAME;
		$orderDir[] = SORT_ASC;
		$order = array ();

		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], true).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects < 0)
		{
			$maxObjects = null;
		}

		$this->connection->setLimit(intval($maxObjects),intval($offset));
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		return new DatabaseUserResultSet($this, $res);
	}

	//Inherited.
	function retrieve_version_type_quota($user, $type)
	{
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
	}
}
?>