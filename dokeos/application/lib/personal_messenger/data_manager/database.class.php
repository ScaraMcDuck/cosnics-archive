<?php
require_once dirname(__FILE__).'/../personalmessengerdatamanager.class.php';
require_once dirname(__FILE__).'/database/databasepersonalmessagepublicationresultset.class.php';
require_once 'MDB2.php';

class DatabasePersonalMessengerDataManager extends PersonalMessengerDataManager {

	private $prefix;
	private $repoDM;
	
	function initialize()
	{
		PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array (get_class(), 'handle_error'));
		$this->repoDM = & RepositoryDataManager :: get_instance();
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string_personal_messenger'),array('debug'=>3,'debug_handler'=>array('PersonalMessengerDatamanager','debug')));
		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query('SET NAMES utf8');
	}
	
	/**
	 * Escapes a column name
	 * @param string $name
	 */
	private function escape_column_name($name)
	{
		list($table, $column) = explode('.', $name, 2);
		$prefix = '';

		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
		return $prefix.$this->connection->quoteIdentifier($name);
	}
	
	static function handle_error($error)
	{
		die(__FILE__.':'.__LINE__.': '.$error->getMessage()
		// For debugging only. May create a security hazard.
		.' ('.$error->getDebugInfo().')');
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
	
	function escape_table_name($name)
	{
		global $personal_messenger_database;
		$database_name = $this->connection->quoteIdentifier($personal_messenger_database);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
	}
	
	/**
	 * Gets the full name of a given table (by adding the database name and a
	 * prefix if required)
	 * @param string $name
	 */
	private function get_table_name($name)
	{
		global $personal_messenger_database;
		return $personal_messenger_database.'.'.$this->prefix.$name;
	}
	
	/**
	 * Translates any type of condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof AggregateCondition)
		{
			return $this->translate_aggregate_condition($condition, & $params, $prefix_learning_object_properties);
		}
		elseif ($condition instanceof InCondition)
		{
			return $this->translate_in_condition($condition, & $params, $prefix_learning_object_properties);
		}
		elseif ($condition instanceof Condition)
		{
			return $this->translate_simple_condition($condition, & $params, $prefix_learning_object_properties);
		}
		else
		{
			die('Need a Condition instance');
		}
	}

	/**
	 * Translates an aggregate condition to a SQL WHERE clause.
	 * @param AggregateCondition $condition The AggregateCondition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_aggregate_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof AndCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params, $prefix_learning_object_properties);
			}
			return '('.implode(' AND ', $cond).')';
		}
		elseif ($condition instanceof OrCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params, $prefix_learning_object_properties);
			}
			return '('.implode(' OR ', $cond).')';
		}
		elseif ($condition instanceof NotCondition)
		{
			return 'NOT ('.$this->translate_condition($condition->get_condition(), & $params, $prefix_learning_object_properties) . ')';
		}
		else
		{
			die('Cannot translate aggregate condition');
		}
	}

	/**
	 * Translates an in condition to a SQL WHERE clause.
	 * @param InCondition $condition The InCondition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_in_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof InCondition)
		{
			$name = $condition->get_name();
			$where_clause = $this->escape_column_name($name).' IN (';
			$values = $condition->get_values();
			$placeholders = array();
			foreach($values as $index => $value)
			{
				$placeholders[] = '?';
				$params[] = $value;
			}
			$where_clause .= implode(',',$placeholders).')';
			return $where_clause;
		}
		else
		{
			die('Cannot translate in condition');
		}
	}

	/**
	 * Translates a simple condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The WHERE clause.
	 */
	function translate_simple_condition($condition, & $params, $prefix_learning_object_properties = false)
	{
		if ($condition instanceof EqualityCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (self :: is_date_column($name))
			{
				$value = self :: to_db_date($value);
			}
			if (is_null($value))
			{
				return $this->escape_column_name($name).' IS NULL';
			}
			$params[] = $value;
			return $this->escape_column_name($name, $prefix_learning_object_properties).' = ?';
		}
		elseif ($condition instanceof InequalityCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (self :: is_date_column($name))
			{
				$value = self :: to_db_date($value);
			}
			$params[] = $value;
			switch ($condition->get_operator())
			{
				case InequalityCondition :: GREATER_THAN :
					$operator = '>';
					break;
				case InequalityCondition :: GREATER_THAN_OR_EQUAL :
					$operator = '>=';
					break;
				case InequalityCondition :: LESS_THAN :
					$operator = '<';
					break;
				case InequalityCondition :: LESS_THAN_OR_EQUAL :
					$operator = '<=';
					break;
				default :
					die('Unknown operator for inequality condition');
			}
			return $this->escape_column_name($name, $prefix_learning_object_properties).' '.$operator.' ?';
		}
		elseif ($condition instanceof PatternMatchCondition)
		{
			$params[] = $this->translate_search_string($condition->get_pattern());
			return $this->escape_column_name($condition->get_name(), $prefix_learning_object_properties).' LIKE ?';
		}
		else
		{
			die('Cannot translate condition');
		}
	}
	
	function get_next_personal_message_publication_id()
	{
		return $this->connection->nextID($this->get_table_name('personal_messenger_publication'));
	}
	
    function count_personal_message_publications($condition = null)
    {
		$query = 'SELECT COUNT('.$this->escape_column_name(PersonalMessagePublication :: PROPERTY_ID).') FROM '.$this->escape_table_name('personal_messenger_publication');

		$params = array ();
		if (isset ($condition))
		{
			// TODO: SCARA - Exclude category from learning object count
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		return $record[0];
    }
    
    function retrieve_personal_message_publication($id)
	{
		
		$query = 'SELECT * FROM '.$this->escape_table_name('personal_messenger_publication').' WHERE '.$this->escape_column_name(PersonalMessagePublication :: PROPERTY_ID).'=?';
		
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_personal_message_publication($record);
	}
    
    function retrieve_personal_message_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		
		$query = 'SELECT * FROM ';
		$query .= $this->escape_table_name('personal_messenger_publication');

		$params = array ();
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		/*
		 * Always respect display order as a last resort.
		 */
//		$orderBy[] = LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX;
//		$orderDir[] = SORT_ASC;
		$order = array ();
		/*
		 * Categories always come first. Does not matter if we're dealing with
		 * a single type.
		 */
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
		return new DatabasePersonalMessagePublicationResultSet($this, $res);
	}
	
	function record_to_personal_message_publication($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (PersonalMessagePublication :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new PersonalMessagePublication($record[PersonalMessagePublication :: PROPERTY_ID], $defaultProp);		
	}
	
	static function is_date_column($name)
	{
		return ($name == PersonalMessagePublication :: PROPERTY_PUBLISHED);
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
}
?>