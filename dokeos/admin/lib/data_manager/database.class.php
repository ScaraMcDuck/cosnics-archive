<?php
/**
 * @package admin
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_setting_result_set.class.php';
require_once dirname(__FILE__).'/database/database_language_result_set.class.php';
require_once dirname(__FILE__).'/database/database_registration_result_set.class.php';
require_once dirname(__FILE__).'/../admin_data_manager.class.php';
require_once dirname(__FILE__).'/../language.class.php';
require_once dirname(__FILE__).'/../registration.class.php';
require_once dirname(__FILE__).'/../setting.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_library_path().'database/connection.class.php';
require_once 'MDB2.php';

class DatabaseAdminDataManager extends AdminDataManager
{
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
		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));
		
		$this->prefix = 'admin_';
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
//		elseif ($prefix_user_object_properties && self :: is_user_column($name))
//		{
//			$prefix = self :: ALIAS_USER_TABLE.'.';
//		}
		return $prefix.$this->connection->quoteIdentifier($name);
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
	 * Executes a query
	 * @param string $query The query (which will be used in a prepare-
	 * statement)
	 * @param int $limit The number of rows
	 * @param int $offset The offset
	 * @param array $params The parameters to replace the placeholders in the
	 * query
	 * @param boolean $is_manip Is the query a manipulation query
	 */
	private function limitQuery($query,$limit,$offset,$params,$is_manip = false)
	{
		$this->connection->setLimit($limit,$offset);
		$statement = $this->connection->prepare($query,null,($is_manip ? MDB2_PREPARE_MANIP : null));
		$res = $statement->execute($params);
		return $res;
	}
	
	static function is_date_column($name)
	{
		// TODO: Temporary bugfix, publication dates were recognized as LO-dates and wrongfully converted
		return false;
		//return ($name == LearningObject :: PROPERTY_CREATION_DATE || $name == LearningObject :: PROPERTY_MODIFICATION_DATE);
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
	
	function record_to_language($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Language :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new Language($record[Language :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_registration($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Registration :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new Registration($record[Registration :: PROPERTY_ID], $defaultProp);
	}
	
	function record_to_setting($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Setting :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}

		return new Setting($record[Setting :: PROPERTY_ID], $defaultProp);
	}
	
    function retrieve_languages($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$query = 'SELECT * FROM ';
		$query .= $this->escape_table_name('language');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		/*
		 * Always respect alphabetical order as a last resort.
		 */
		$orderBy[] = Language :: PROPERTY_ORIGINAL_NAME;
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
		return new DatabaseLanguageResultSet($this, $res);
	}
	
    function retrieve_settings($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{

		$query = 'SELECT * FROM ';
		$query .= $this->escape_table_name('setting');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		/*
		 * Always respect alphabetical order as a last resort.
		 */
		$orderBy[] = Setting :: PROPERTY_VARIABLE;
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
		return new DatabaseSettingResultSet($this, $res);
	}
	
	function retrieve_registrations($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$query = 'SELECT * FROM ';
		$query .= $this->escape_table_name('registration');

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		/*
		 * Always respect alphabetical order as a last resort.
		 */
		$orderBy[] = Registration :: PROPERTY_NAME;
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
		return new DatabaseRegistrationResultSet($this, $res);
	}
	
	function retrieve_language_from_english_name($english_name)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('language').' WHERE '.$this->escape_column_name(Language :: PROPERTY_ENGLISH_NAME).'=?';
		$res = $this->limitQuery($query, 1, null, array ($english_name));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_language($record);
	}
	
	function retrieve_setting_from_variable_name($variable, $application = 'admin')
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('setting').' WHERE '.$this->escape_column_name(Setting :: PROPERTY_APPLICATION).'=? AND '.$this->escape_column_name(Setting :: PROPERTY_VARIABLE).'=?';
		$res = $this->limitQuery($query, 1, null, array ($application, $variable));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		return $this->record_to_setting($record);
	}
	
	function update_setting($setting)
	{
		$where = $this->escape_column_name(Setting :: PROPERTY_ID).'='.$setting->get_id();
		$props = array();
		foreach ($setting->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('setting'), $props, MDB2_AUTOQUERY_UPDATE, $where);

		return true;
	}
	
	function update_registration($registration)
	{
		$where = $this->escape_column_name(Registration :: PROPERTY_ID).'='.$registration->get_id();
		$props = array();
		foreach ($registration->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('registration'), $props, MDB2_AUTOQUERY_UPDATE, $where);

		return true;
	}
	
	function delete_registration($registration)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('registration').' WHERE '.$this->escape_column_name(Registration :: PROPERTY_ID).'=?';
		$statement = $this->connection->prepare($query);
		if ($statement->execute($registration->get_id()))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	// Inherited.
	function get_next_language_id()
	{
		return $this->connection->nextID($this->get_table_name('language'));
	}
	
	// Inherited.
	function get_next_registration_id()
	{
		return $this->connection->nextID($this->get_table_name('registration'));
	}
	
	// Inherited.
	function get_next_setting_id()
	{
		return $this->connection->nextID($this->get_table_name('setting'));
	}
	
	function create_language($language)
	{
		$props = array();
		foreach ($language->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(Language :: PROPERTY_ID)] = $language->get_id();

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('language'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_registration($registration)
	{
		$props = array();
		foreach ($registration->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(Registration :: PROPERTY_ID)] = $registration->get_id();

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('registration'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function create_setting($setting)
	{
		$props = array();
		foreach ($setting->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(Setting :: PROPERTY_ID)] = $setting->get_id();

		$this->connection->loadModule('Extended');
		if ($this->connection->extended->autoExecute($this->get_table_name('setting'), $props, MDB2_AUTOQUERY_INSERT))
		{
			return true;
		}
		else
		{
			return false;
		}
	}	
}
?>