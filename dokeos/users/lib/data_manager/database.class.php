<?php
/**
 * @package users
 * @subpackage datamanager
 */
//require_once dirname(__FILE__).'/database/databaselearningobjectresultset.class.php';
require_once dirname(__FILE__).'/../usersdatamanager.class.php';
//require_once dirname(__FILE__).'/../configuration.class.php';
//require_once dirname(__FILE__).'/../learningobject.class.php';
//require_once dirname(__FILE__).'/../condition/condition.class.php';
//require_once dirname(__FILE__).'/../condition/equalitycondition.class.php';
//require_once dirname(__FILE__).'/../condition/inequalitycondition.class.php';
//require_once dirname(__FILE__).'/../condition/patternmatchcondition.class.php';
//require_once dirname(__FILE__).'/../condition/aggregatecondition.class.php';
//require_once dirname(__FILE__).'/../condition/andcondition.class.php';
//require_once dirname(__FILE__).'/../condition/orcondition.class.php';
//require_once dirname(__FILE__).'/../condition/notcondition.class.php';
//require_once dirname(__FILE__).'/../condition/incondition.class.php';
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
//	const ALIAS_LEARNING_OBJECT_VERSION_TABLE = 'lov';
//	const ALIAS_LEARNING_OBJECT_ATTACHMENT_TABLE = 'loa';
//	const ALIAS_TYPE_TABLE = 'tt';
//	const ALIAS_LEARNING_OBJECT_PARENT_TABLE = 'lop';

	/**
	 * The database connection.
	 */
	private $connection;

	/**
	 * The table name prefix, if any.
	 */
	private $prefix;

	function initialize()
	{
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_user'),array('debug'=>3,'debug_handler'=>array('UsersDatamanager','debug')));
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
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
		list($table, $column) = explode('.', $name, 2);
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
		return $prefix.$this->connection->quoteIdentifier($name);
	}
	
	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table identifier.
	 * @return string The escaped table name.
	 */
	function escape_table_name($name)
	{
		global $user_database;
		$database_name = $this->connection->quoteIdentifier($user_database);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}
	
	private static function is_user_column($name)
	{
		return User :: is_default_property_name($name); //|| $name == User :: PROPERTY_TYPE || $name == User :: PROPERTY_DISPLAY_ORDER_INDEX || $name == User :: PROPERTY_USER_ID;
	}
	
	// Inherited.
	function update_user($user)
	{
		$where = $this->escape_column_name(User :: PROPERTY_ID).'='.$user->get_user_id();
		$props = array();
		foreach ($user->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('user'), $props, MDB2_AUTOQUERY_UPDATE, $where);

		return true;
	}
	
	// Inherited.
	function delete_user($user)
	{
		if(!$this->user_deletion_allowed($user))
		{
			return false;
		}

		// TODO: call delete_learning_object_by_user($user_id) in repdatamngr
		
		// Delete the user from the database
		$query = 'DELETE FROM '.$this->escape_table_name('user').' WHERE '.$this->escape_column_name('user_id').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($user->get_id(), $user->get_id()));
		
		return true;
	}
	
	// Inherited.
	function create_user($user)
	{
		$props = array();
		foreach ($user->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(User :: PROPERTY_USER_ID)] = $user->get_user_id();
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('user'), $props, MDB2_AUTOQUERY_INSERT);
		return true;
	}
	
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
		$manager->createTable($name,$properties,$options);
		foreach($indexes as $index_name => $index_info)
		{
			if($index_info['type'] == 'primary')
			{
				$index_info['primary'] = 1;
				$manager->createConstraint($name,$index_name,$index_info);
			}
			else
			{
				$manager->createIndex($name,$index_name,$index_info);
			}
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
		global $user_database;
		return $user_database.'.'.$this->prefix.$name;
	}
	
	function retrieve_user($id, $type = null)
	{
//		if (is_null($type))
//		{
//			$type = $this->determine_learning_object_type($id);
//		}
//		if ($this->is_extended_type($type))
//		{
//			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' JOIN '.$this->escape_table_name($type).' AS '.self :: ALIAS_TYPE_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).'='.self :: ALIAS_TYPE_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).' WHERE '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
//		}
//		else
//		{
//			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
//		}
//		$this->connection->setLimit(1);
//		$statement = $this->connection->prepare($query);
//		$res = $statement->execute($id);
//		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
//		$res->free();
//		return self :: record_to_learning_object($record, true);
	}
	
	function is_username_available($username)
	{
		$query = 'SELECT username FROM '.$this->escape_table_name('user').' WHERE '.$this->escape_column_name(User :: PROPERTY_USERNAME).'=?';
		$statement = $this->connection->prepare($query);
		$result = $statement->execute($username);
		if ($result->numRows() == 1)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
}
?>