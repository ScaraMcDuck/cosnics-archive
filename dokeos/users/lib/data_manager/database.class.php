<?php
/**
 * @package repository
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/databaselearningobjectresultset.class.php';
require_once dirname(__FILE__).'/../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../configuration.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';
require_once dirname(__FILE__).'/../condition/condition.class.php';
require_once dirname(__FILE__).'/../condition/equalitycondition.class.php';
require_once dirname(__FILE__).'/../condition/inequalitycondition.class.php';
require_once dirname(__FILE__).'/../condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/../condition/aggregatecondition.class.php';
require_once dirname(__FILE__).'/../condition/andcondition.class.php';
require_once dirname(__FILE__).'/../condition/orcondition.class.php';
require_once dirname(__FILE__).'/../condition/notcondition.class.php';
require_once dirname(__FILE__).'/../condition/incondition.class.php';
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

class DatabaseRepositoryDataManager extends UsersDataManager
{
//	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';
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

	// Inherited.
	function initialize()
	{
		PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array (get_class(), 'handle_error'));
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_repository'),array('debug'=>3,'debug_handler'=>array('DatabaseRepositoryDataManager','debug')));
		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
	}
	
	function update_user($user)
	{
		$where = $this->escape_column_name(UserObject :: PROPERTY_ID).'='.$user->get_user_id();
		$props = array();
		foreach ($user->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		// ...
	}
	
	function delete_user_object($user)
	{
		if( !$this->user_object_deletion_allowed($user))
		{
			return false;
		}

		// Delete the user from the database
		$query = 'DELETE FROM '.$this->escape_table_name('user').' WHERE '.$this->escape_column_name('learning_object').'=? OR '.$this->escape_column_name('user_id').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($object->get_id(), $object->get_id()));

		// TODO: remove the user his objects from the repository DB
		
		return true;
	}
	
}
?>