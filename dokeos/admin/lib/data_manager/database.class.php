<?php
/**
 * @package admin
 * @subpackage datamanager
 */
//require_once dirname(__FILE__).'/database/databasesettingsresultset.class.php';
require_once dirname(__FILE__).'/../admindatamanager.class.php';
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
	private $repoDM;
	
	function initialize()
	{
		PEAR :: setErrorHandling(PEAR_ERROR_CALLBACK, array (get_class(), 'handle_error'));
		$conf = Configuration :: get_instance();
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),array('debug'=>3,'debug_handler'=>array('DatabaseAdminDatamanager','debug')));
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
		if(is_array($condition))
		{
			if(count($condition) == 1)
			{
				$condition = $condition[0];
			}
			else
			{
				$condition = new AndCondition($condition);
			}
		}
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
		elseif ($condition instanceof LikeCondition)
		{
			$name = $condition->get_name();
			$value = $condition->get_value();
			if (is_null($value))
			{
				return $this->escape_column_name($name).' IS NULL';
			}
			$params[] = $value;
			return $this->escape_column_name($name, $prefix_learning_object_properties).' LIKE ?';
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
}
?>