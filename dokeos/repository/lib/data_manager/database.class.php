<?php
require_once dirname(__FILE__).'/../repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../configuration.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';
require_once dirname(__FILE__).'/../condition/condition.class.php';
require_once dirname(__FILE__).'/../condition/exactmatchcondition.class.php';
require_once dirname(__FILE__).'/../condition/patternmatchcondition.class.php';
require_once dirname(__FILE__).'/../condition/aggregatecondition.class.php';
require_once dirname(__FILE__).'/../condition/andcondition.class.php';
require_once dirname(__FILE__).'/../condition/orcondition.class.php';
require_once dirname(__FILE__).'/../condition/notcondition.class.php';
require_once 'DB.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
==============================================================================
 */

class DatabaseRepositoryDataManager extends RepositoryDataManager
{
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
		$this->connection = DB :: connect($conf->get_parameter('database', 'connection_string'));
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
	}

	// Inherited.
	function determine_learning_object_type($id)
	{
		$sth = $this->connection->prepare('SELECT '.$this->escape_column_name('type').' FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name('id').'=? LIMIT 1');
		$res = & $this->connection->execute($sth, $id);
		$record = $res->fetchRow(DB_FETCHMODE_ORDERED);
		return $record[0];
	}

	// Inherited.
	function retrieve_learning_object($id, $type = null)
	{
		if (is_null($type))
		{
			$type = $this->determine_learning_object_type($id);
		}
		if ($this->is_extended_type($type))
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS t1'.' JOIN '.$this->escape_table_name($type).' AS t2 ON t1.'.$this->escape_column_name('id').'=t2.'.$this->escape_column_name('id').' WHERE t1.'.$this->escape_column_name('id').'=? LIMIT 1';
		}
		else
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name('id').'=? LIMIT 1';
		}
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $id);
		$record = $res->fetchRow(DB_FETCHMODE_ASSOC);
		return self :: record_to_learning_object($record);
	}

	// Inherited.
	// TODO: Extract methods.
	function retrieve_learning_objects($type = null, $conditions = null, $orderBy = array (), $orderDir = array (), $firstIndex = 0, $maxObjects = -1)
	{
		if (isset ($type))
		{
			if ($this->is_extended_type($type))
			{
				$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS t1 JOIN '.$this->escape_table_name($type).' AS t2 ON t1.'.$this->escape_column_name('id').' = t2.'.$this->escape_column_name('id');
			}
			else
			{
				$query = 'SELECT * FROM '.$this->escape_table_name('learning_object');
				$match = new ExactMatchCondition('type', $type);
				$conditions = isset ($conditions) ? new AndCondition(array ($match, $conditions)) : $match;
			}
		}
		else
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object');
		}
		$params = array ();
		if (isset ($conditions))
		{
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		}
		$order = array ();
		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i]).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
		}
		if (count($order))
		{
			$query .= ' ORDER BY '.implode(', ', $order);
		}
		if ($maxObjects > 0)
		{
			if ($firstIndex > 0)
			{
				$query .= ' LIMIT '.$firstIndex.','.$maxObjects;
			}
			else
			{
				$query .= ' LIMIT '.$maxObjects;
			}
		}
		elseif ($firstIndex > 0)
		{
			$query .= ' LIMIT '.$firstIndex.',999999999999';
		}
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $params);
		$objects = array ();
		if (isset ($type))
		{
			while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
			{
				$objects[] = self :: record_to_learning_object($record);
			}
		}
		else
		{
			/*
			 * TODO: Extend so additional properties can be fetched when
			 * needed. This would probably involve reviewing LearningObject's
			 * additional property accessor methods.
			 */
			while ($record = $res->fetchRow(DB_FETCHMODE_ASSOC))
			{
				if ($this->is_extended_type($record['type']))
				{
					$objects[] = $this->retrieve_learning_object($record['id'], $record['type']);
				}
				else
				{
					$objects[] = self :: record_to_learning_object($record);
				}
			}
		}
		return $objects;
	}

	// Inherited.
	// TODO: Extract methods; share stuff with retrieve_learning_objects.
	function count_learning_objects($type = null, $conditions = null)
	{
		if (isset ($type))
		{
			if ($this->is_extended_type($type))
			{
				$query = 'SELECT COUNT(t1.'.$this->escape_column_name('id').') FROM '.$this->escape_table_name('learning_object').' AS t1 JOIN '.$this->escape_table_name($type).' AS t2 ON t1.'.$this->escape_column_name('id').' = t2.'.$this->escape_column_name('id');
			}
			else
			{
				$query = 'SELECT COUNT('.$this->escape_column_name('id').') FROM '.$this->escape_table_name('learning_object');
				$match = new ExactMatchCondition('type', $type);
				$conditions = isset ($conditions) ? new AndCondition(array ($match, $conditions)) : $match;
			}
		}
		else
		{
			$query = 'SELECT COUNT('.$this->escape_column_name('id').') FROM '.$this->escape_table_name('learning_object');
		}
		$params = array ();
		if (isset ($conditions))
		{
			$query .= ' WHERE '.$this->translate_condition($conditions, & $params);
		}
		$sth = $this->connection->prepare($query);
		$res = & $this->connection->execute($sth, $params);
		$record = $res->fetchRow(DB_FETCHMODE_ORDERED);
		return $record[0];
	}

	// Inherited.
	function create_learning_object($object)
	{
		$id = $this->connection->nextId($this->prefix.'learning_object');
		$props = $object->get_default_properties();
		$props['id'] = $id;
		$props['type'] = $object->get_type();
		$props['created'] = self :: to_db_date($props['created']);
		$props['modified'] = self :: to_db_date($props['modified']);
		$this->connection->autoExecute($this->prefix.'learning_object', $props, DB_AUTOQUERY_INSERT);
		if ($object->is_extended())
		{
			$props = $object->get_additional_properties();
			$props['id'] = $id;
			$this->connection->autoExecute($this->prefix.$object->get_type(), $props, DB_AUTOQUERY_INSERT);
		}
		return $id;
	}

	// Inherited.
	function update_learning_object($object)
	{
		$where = $this->escape_column_name('id').'='.$object->get_id();
		$props = $object->get_default_properties();
		$props['created'] = self :: to_db_date($props['created']);
		$props['modified'] = self :: to_db_date($props['modified']);
		$this->connection->autoExecute($this->prefix.'learning_object', $props, DB_AUTOQUERY_UPDATE, $where);
		if ($object->is_extended())
		{
			$this->connection->autoExecute($this->prefix.$object->get_type(), $object->get_additional_properties(), DB_AUTOQUERY_UPDATE, $where);
		}
	}

	// Inherited.
	function delete_learning_object($object)
	{
		$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name('id').'=?');
		$this->connection->execute($sth, $object->get_id());
		if ($object->is_extended())
		{
			$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name($object->get_type()).' WHERE '.$this->escape_column_name('id').'=?');
			$this->connection->execute($sth, $object->get_id());
		}
	}

	// Inherited.
	function delete_all_learning_objects()
	{
		foreach ($this->get_registered_types() as $type)
		{
			if ($this->is_extended_type($type))
			{
				$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name($type));
				$this->connection->execute($sth);
			}
		}
		$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name('learning_object'));
		$this->connection->execute($sth);
	}

	/**
	 * Returns the database connection directly. You should not use this
	 * method, as it only applies for DatabaseRepositoryDataManager, and not
	 * for other RepositoryDataManager implementations. The reason why this
	 * method is accessible is so application data managers that use the same
	 * database may reuse the connection.
	 * @return DB_connection The database connection.
	 */
	function get_connection()
	{
		/*
		 * TODO: Move connection out of the repository libraries, using a
		 *       singleton pattern.
		 */
		return $this->connection;
	}

	/**
	 * Returns the prefix for database table names, if any. This method is
	 * visible for the same reason as get_connection().
	 * @return string The prefix.
	 */
	function get_table_name_prefix()
	{
		return $this->prefix;
	}

	/**
	 * Handles PEAR errors. If an error is encountered, the program dies with
	 * a descriptive error message.
	 * @param DB_Error $error The error object.
	 */
	static function handle_error($error)
	{
		die(__FILE__.':'.__LINE__.': '.$error->getMessage()
		// For debugging only. May create a security hazard.
		.' ('.$error->getDebugInfo().')');
	}

	/**
	 * Converts a datetime value (as retrieved from the database) to a UNIX
	 * timestamp (as returned by time()).
	 * @param string $date The date as a UNIX timestamp.
	 * @return int The date as a UNIX timestamp.
	 */
	private static function from_db_date($date)
	{
		if (isset ($date))
		{
			return strtotime($date);
		}
		return null;
	}

	/**
	 * Converts a UNIX timestamp (as returned by time()) to a datetime string
	 * for use in SQL queries.
	 * @param int $date The date as a UNIX timestamp.
	 * @return string The date in datetime format.
	 */
	private static function to_db_date($date)
	{
		if (isset ($date))
		{
			return date('Y-m-d H:i:s', $date);
		}
		return null;
	}

	/**
	 * Parses a database record fetched as an associative array into a
	 * learning object.
	 * @param array $record The associative array.
	 * @return LearningObject The learning object.
	 */
	private function record_to_learning_object($record)
	{
		$defaultProp = array ();
		foreach (LearningObject :: $DEFAULT_PROPERTIES as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		$defaultProp['created'] = self :: from_db_date($defaultProp['created']);
		$defaultProp['modified'] = self :: from_db_date($defaultProp['modified']);
		$additionalProp = array ();
		$properties = $this->get_additional_properties($record['type']);
		if (count($properties) > 0)
		{
			foreach ($properties as $prop)
			{
				$additionalProp[$prop] = $record[$prop];
			}
		}
		return $this->factory($record['type'], $record['id'], $defaultProp, $additionalProp);
	}

	/**
	 * Translates a string with wildcard characters "?" (single character)
	 * and "*" (any character sequence) to a SQL pattern for use in a LIKE
	 * condition. Should be suitable for any SQL flavor.
	 * @param string $string The string that contains wildcard characters.
	 * @return string The escaped string.
	 */
	private static function translate_search_string($string)
	{
		/*
		======================================================================
		 * A brief explanation of these regexps:
		 * - The first one escapes SQL wildcard characters, thus prefixing
		 *   %, ', \ and _ with a backslash.
		 * - The second one replaces asterisks that are not prefixed with a
		 *   backslash (which escapes them) with the SQL equivalent, namely a
		 *   percent sign.
		 * - The third one is similar to the second: it replaces question
		 *   marks that are not escaped with the SQL equivalent _.
		======================================================================
		 */
		return preg_replace(array ('/([%\'\\\\_])/e', '/(?<!\\\\)\*/', '/(?<!\\\\)\?/'), array ("'\\\\\\\\' . '\\1'", '%', '_'), $string);
	}

	/**
	 * Escapes a column name in accordance with the database type.
	 * @param string $name The column name.
	 * @return string The escaped column name.
	 */
	private function escape_column_name($name)
	{
		return $this->connection->quoteIdentifier($name);
	}

	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table name.
	 * @return string The escaped table name.
	 */
	private function escape_table_name($name)
	{
		return $this->connection->quoteIdentifier($this->prefix.$name);
	}

	/**
	 * Translates any type of condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @return string The WHERE clause.
	 */
	private function translate_condition($condition, & $params)
	{
		if ($condition instanceof AggregateCondition)
		{
			return $this->translate_aggregate_condition($condition, & $params);
		}
		elseif ($condition instanceof Condition)
		{
			return $this->translate_simple_condition($condition, & $params);
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
	 * @return string The WHERE clause.
	 */
	private function translate_aggregate_condition($condition, & $params)
	{
		if ($condition instanceof AndCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params);
			}
			return '('.implode(' AND ', $cond).')';
		}
		elseif ($condition instanceof OrCondition)
		{
			$cond = array ();
			foreach ($condition->get_conditions() as $c)
			{
				$cond[] = $this->translate_condition($c, & $params);
			}
			return '('.implode(' OR ', $cond).')';
		}
		elseif ($condition instanceof NotCondition)
		{
			return 'NOT '.$condition->get_condition();
		}
		else
		{
			die('Cannot translate aggregate condition');
		}
	}

	/**
	 * Translates a simple condition to a SQL WHERE clause.
	 * @param Condition $condition The Condition object.
	 * @param array $params A reference to the query's parameter list.
	 * @return string The WHERE clause.
	 */
	private function translate_simple_condition($condition, & $params)
	{
		if ($condition instanceof ExactMatchCondition)
		{
			$params[] = $condition->get_value();
			return $this->escape_column_name($condition->get_name()).' = ?';
		}
		elseif ($condition instanceof PatternMatchCondition)
		{
			$params[] = $this->translate_search_string($condition->get_pattern());
			return $this->escape_column_name($condition->get_name()).' LIKE ?';
		}
		else
		{
			die('Cannot translate condition');
		}
	}

	/**
	 * Get the disk space used by the given owner.
	 * @return int The number of bytes
	 */
	public function get_used_disk_space($owner)
	{
		$condition_owner = new ExactMatchCondition('owner',$owner);
		$types = $this->get_registered_types();
		foreach($types as $index => $type)
		{
			$class = $this->type_to_class($type);
			$properties = call_user_func(array($class,'get_disk_space_properties'));
			if(is_null($properties))
			{
				continue;
			}
			if( !is_array($properties))
			{
				$properties = array($properties);
			}
			$sum = array();
			foreach($properties as $index => $property)
			{
				$sum[] = 'SUM('.$property.')';
			}
			if ($this->is_extended_type($type))
			{
				$query = 'SELECT '.implode('+',$sum).' AS disk_space FROM '.$this->escape_table_name('learning_object').' AS t1 JOIN '.$this->escape_table_name($type).' AS t2 ON t1.'.$this->escape_column_name('id').' = t2.'.$this->escape_column_name('id');
				$condition = $condition_owner;
			}
			else
			{
				$query = 'SELECT '.implode('+',$sum).' AS disk_space FROM '.$this->escape_table_name('learning_object');
				$match = new ExactMatchCondition('type', $type);
				$condition = new AndCondition(array ($match, $condition_owner));
			}
			$params = array ();
			$query .= ' WHERE '.$this->translate_condition($condition, & $params);
			$sth = $this->connection->prepare($query);
			$res = & $this->connection->execute($sth, $params);
			$record = $res->fetchRow(DB_FETCHMODE_OBJECT);
			$disk_space += $record->disk_space;
		}
		return $disk_space;
	}

}
?>