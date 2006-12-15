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

class DatabaseRepositoryDataManager extends RepositoryDataManager
{
	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';
	const ALIAS_TYPE_TABLE = 'tt';
	const ALIAS_LEARNING_OBJECT_PARENT_TABLE = 'lop';

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
		$this->connection = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),array('debug'=>3,'debug_handler'=>array('DatabaseRepositoryDataManager','debug')));
		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
		$this->connection->query("SET NAMES utf8");
	}

	/**
	 * This function can be used to handle some debug info from MDB2
	 */
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

	// Inherited.
	function determine_learning_object_type($id)
	{
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare('SELECT '.$this->escape_column_name(LearningObject :: PROPERTY_TYPE).' FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?');
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
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
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' JOIN '.$this->escape_table_name($type).' AS '.self :: ALIAS_TYPE_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).'='.self :: ALIAS_TYPE_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).' WHERE '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
		}
		else
		{
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
		}
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		return self :: record_to_learning_object($record, true);
	}

	// Inherited.
	// TODO: Extract methods.
	function retrieve_learning_objects($type = null, $condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		$query = 'SELECT * FROM ';
		if ($different_parent_state)
		{
			/*
			 * Making parent table come first makes sure the properties we
			 * need come last, so they are actually in the associative array
			 * representing the record.
			 */
			$query .= $this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_PARENT_TABLE.' JOIN ';
		}
		if (isset ($type))
		{
			if ($this->is_extended_type($type))
			{
				$query .= $this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' JOIN '.$this->escape_table_name($type).' AS '.self :: ALIAS_TYPE_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).' = '.self :: ALIAS_TYPE_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID);
			}
			else
			{
				$query .= $this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE;
				$match = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
				$condition = isset ($condition) ? new AndCondition($match, $condition) : $match;
			}
		}
		else
		{
			$query .= $this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE;
		}
		if ($state >= 0)
		{
			$conds = array();
			if ($different_parent_state)
			{
				$query .= ' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_PARENT_ID).' = '.self :: ALIAS_LEARNING_OBJECT_PARENT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID);
				$conds[] = new NotCondition(new EqualityCondition(self :: ALIAS_LEARNING_OBJECT_PARENT_TABLE.'.'.LearningObject :: PROPERTY_STATE, $state));
			}
			$conds[] = new EqualityCondition(self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.LearningObject :: PROPERTY_STATE, $state);
			if (isset($condition))
			{
				$conds[] = $condition;
			}
			$condition = new AndCondition($conds);
		}
		$params = array ();
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		/*
		 * Always respect display order as a last resort.
		 */
		$orderBy[] = LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX;
		$orderDir[] = SORT_ASC;
		$order = array ();
		/*
		 * Categories always come first. Does not matter if we're dealing with
		 * a single type.
		 */
		if (!isset($type))
		{
			$order[] = '('.$this->escape_column_name(LearningObject :: PROPERTY_TYPE, true).' = "category") DESC';
		}
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
		return new DatabaseLearningObjectResultSet($this, $res, isset($type));
	}

	// Inherited.
	function retrieve_additional_learning_object_properties($learning_object)
	{
		$type = $learning_object->get_type();
		if (!$this->is_extended_type($type))
		{
			return array();
		}
		$id = $learning_object->get_id();
		$query = 'SELECT '.implode(',', array_map(array(self, 'escape_column_name'), $this->get_additional_properties($type))).' FROM '.$this->escape_table_name($type).' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($id);
		return $res->fetchRow(MDB2_FETCHMODE_ASSOC);
	}

	// Inherited.
	// TODO: Extract methods; share stuff with retrieve_learning_objects.
	function count_learning_objects($type = null, $condition = null, $state = LearningObject :: STATE_NORMAL, $different_parent_state = false)
	{
		if (isset ($type))
		{
			if ($this->is_extended_type($type))
			{
				$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' JOIN '.$this->escape_table_name($type).' AS '.self :: ALIAS_TYPE_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).' = '.self :: ALIAS_TYPE_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID);
			}
			else
			{
				$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE;
				$match = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
				$condition = isset ($condition) ? new AndCondition(array ($match, $condition)) : $match;
			}
		}
		else
		{
			$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE;
		}
		if ($state >= 0)
		{
			$conds = array();
			if ($different_parent_state)
			{
				$query .= ' JOIN '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_PARENT_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_PARENT_ID).' = '.self :: ALIAS_LEARNING_OBJECT_PARENT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID);
				$conds[] = new NotCondition(new EqualityCondition(self :: ALIAS_LEARNING_OBJECT_PARENT_TABLE.'.'.LearningObject :: PROPERTY_STATE, $state));
			}
			$conds[] = new EqualityCondition(self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.LearningObject :: PROPERTY_STATE, $state);
			if (isset($condition))
			{
				$conds[] = $condition;
			}
			$condition = new AndCondition($conds);
		}
		$params = array ();
		if (isset ($condition))
		{
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
		}
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		return $record[0];
	}

	// Inherited.
	function get_next_learning_object_id()
	{
		$id = $this->connection->nextID($this->get_table_name('learning_object'));
		return $id;
	}

	// Inherited.
	function create_learning_object($object)
	{
		$props = array();
		foreach ($object->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(LearningObject :: PROPERTY_ID)] = $object->get_id();
		$props[$this->escape_column_name(LearningObject :: PROPERTY_TYPE)] = $object->get_type();
		$props[$this->escape_column_name(LearningObject :: PROPERTY_CREATION_DATE)] = self :: to_db_date($object->get_creation_date());
		$props[$this->escape_column_name(LearningObject :: PROPERTY_MODIFICATION_DATE)] = self :: to_db_date($object->get_modification_date());
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('learning_object'), $props, MDB2_AUTOQUERY_INSERT);
		if ($object->is_extended())
		{
			$props = array();
			foreach ($object->get_additional_properties() as $key => $value)
			{
				$props[$this->escape_column_name($key)] = $value;
			}
			$props[$this->escape_column_name(LearningObject :: PROPERTY_ID)] = $object->get_id();
			$this->connection->extended->autoExecute($this->get_table_name($object->get_type()), $props, MDB2_AUTOQUERY_INSERT);
		}
		return true;
	}

	// Inherited.
	function update_learning_object($object)
	{
		$where = $this->escape_column_name(LearningObject :: PROPERTY_ID).'='.$object->get_id();
		$props = array();
		foreach ($object->get_default_properties() as $key => $value)
		{
			$props[$this->escape_column_name($key)] = $value;
		}
		$props[$this->escape_column_name(LearningObject :: PROPERTY_CREATION_DATE)] = self :: to_db_date($object->get_creation_date());
		$props[$this->escape_column_name(LearningObject :: PROPERTY_MODIFICATION_DATE)] = self :: to_db_date($object->get_modification_date());
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('learning_object'), $props, MDB2_AUTOQUERY_UPDATE, $where);
		if ($object->is_extended())
		{
			$props = array();
			foreach ($object->get_additional_properties() as $key => $value)
			{
				$props[$this->escape_column_name($key)] = $value;
			}
			$this->connection->extended->autoExecute($this->get_table_name($object->get_type()), $props, MDB2_AUTOQUERY_UPDATE, $where);
		}
		return true;
	}

	// Inherited.
	function delete_learning_object($object)
	{
		if( !$this->learning_object_deletion_allowed($object))
		{
			return false;
		}
		// Delete children
		$condition = new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $object->get_id());
		$children = $this->retrieve_learning_objects(null, $condition, array (), array (), 0, -1, LearningObject :: STATE_RECYCLED)->as_array();
		$children_deleted = true;
		foreach ($children as $index => $child)
		{
			$child_deleted = $this->delete_learning_object($child);
			$children_deleted = $children_deleted && $child_deleted;
		}
		// If all children deleted -> delete object
		if ($children_deleted)
		{
			// Delete all attachments (only the links, not the actual objects)
			$query = 'DELETE FROM '.$this->escape_table_name('learning_object_attachment').' WHERE '.$this->escape_column_name('learning_object').'=? OR '.$this->escape_column_name('attachment').'=?';
			$sth = $this->connection->prepare($query);
			$res = $sth->execute(array($object->get_id(), $object->get_id()));

			// Delete object
			$query = 'DELETE FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
			$statement = $this->connection->prepare($query);
			$statement->execute($object->get_id());

			if ($object->is_extended())
			{
				$query = 'DELETE FROM '.$this->escape_table_name($object->get_type()).' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
				$statement = $this->connection->prepare($query);
				$statement->execute($object->get_id());
			}
			$query = 'UPDATE '.$this->escape_table_name('learning_object').' SET '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'='.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'-1 WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'>?';
			$statement = $this->connection->prepare($query);
			$statement->execute($object->get_display_order_index());
			return true;
		}
		return false;
	}

	// Inherited.
	function delete_all_learning_objects()
	{
		foreach ($this->get_registered_types() as $type)
		{
			if ($this->is_extended_type($type))
			{
				$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name($type));
				$sth->execute();
			}
		}
		$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name('learning_object'));
		$sth->execute();
	}

	// Inherited.
	function move_learning_object($object, $places)
	{
		if ($places < 0)
		{
			return $this->move_learning_object_up($object, - $places);
		}
		else
		{
			return $this->move_learning_object_down($object, $places);
		}
	}

	private function move_learning_object_up($object, $places)
	{
		$oldIndex = $object->get_display_order_index();
		$query = 'UPDATE '.$this->escape_table_name('learning_object').' SET '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'='.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'+1 WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_PARENT_ID).'=? AND '.$this->escape_column_name(LearningObject :: PROPERTY_TYPE).'=? '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'<? ORDER BY '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).' DESC';
		$this->connection->setLimit($places);
		$statement = $this->connection->prepare($query);
		$statement->execute(array($object->get_parent_id(), $object->get_type(), $oldIndex));
		$rowsMoved = $this->connection->affectedRows();
		$query = 'UPDATE '.$this->escape_table_name('learning_object').' SET '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'=? WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$statement->execute(array($oldIndex - $places, $object->get_id()));
		return $rowsMoved;
	}

	private function move_learning_object_down($object, $places)
	{
		$oldIndex = $object->get_display_order_index();
		$query = 'UPDATE '.$this->escape_table_name('learning_object').' SET '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'='.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'-1 WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_PARENT_ID).'=? AND '.$this->escape_column_name(LearningObject :: PROPERTY_TYPE).'=? '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'>? ORDER BY '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).' ASC';
		$this->connection->setLimit($places);
		$statement = $this->connection->prepare($query);
		$statement->execute(array($object->get_parent_id(), $publication->get_type(), $oldIndex));
		$rowsMoved = $this->connection->affectedRows();
		$query = 'UPDATE '.$this->escape_table_name('learning_object').' SET '.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).'=? WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$statement->execute(array($oldIndex + $places, $publication->get_id()));
		return $rowsMoved;
	}

	// Inherited.
	function get_next_learning_object_display_order_index($parent, $type)
	{
		$query = 'SELECT MAX('.$this->escape_column_name(LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX).') AS h FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_PARENT_ID).'=? AND '.$this->escape_column_name(LearningObject :: PROPERTY_TYPE).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute(array ($parent, $type));
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();
		$highest_index = $record['h'];
		if (!is_null($highest_index))
		{
			return $highest_index +1;
		}
		return 1;
	}

	// Inherited.
	function retrieve_attached_learning_objects ($object)
	{
		$id = $object->get_id();
		$query = 'SELECT '.$this->escape_column_name('attachment').' FROM '.$this->escape_table_name('learning_object_attachment').' WHERE '.$this->escape_column_name('learning_object').'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($id);
		$attachments = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ORDERED))
		{
			$attachments[] = $this->retrieve_learning_object($record[0]);
		}
		$res->free();
		return $attachments;
	}

	// Inherited.
	function attach_learning_object ($object, $attachment_id)
	{
		$props = array();
		$props['learning_object'] = $object->get_id();
		$props['attachment'] = $attachment_id;
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('learning_object_attachment'), $props, MDB2_AUTOQUERY_INSERT);
	}

	// Inherited.
	function detach_learning_object ($object, $attachment_id)
	{
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_attachment').' WHERE '.$this->escape_column_name('learning_object').'=? AND '.$this->escape_column_name('attachment').'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$affectedRows = $statement->execute(array ($object->get_id(), $attachment_id));
		return ($affectedRows > 0);
	}

	// Inherited.
	function set_learning_object_states ($object_ids, $state)
	{
		if (!count($object_ids))
		{
			return true;
		}
		$query = 'UPDATE '.$this->escape_table_name('learning_object').' SET '.$this->escape_column_name(LearningObject :: PROPERTY_STATE).'=? WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).' IN (?'.str_repeat(',?', count($object_ids) - 1).')';
		$params = $object_ids;
		array_unshift($params, $state);
		$this->connection->setLimit(count($object_ids));
		$statement = $this->connection->prepare($query);
		$affectedRows = $statement->execute($params);
		return ($affectedRows == count($object_ids));
	}

	// Inherited.
	function get_children_ids($object)
	{
		$children_ids = array();
		$parent_ids = array($object->get_id());
		do
		{
			$query = 'SELECT '.$this->escape_column_name(LearningObject :: PROPERTY_ID).' FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_PARENT_ID).' IN (?'.str_repeat(',?',count($parent_ids)-1).')';
			$statement = $this->connection->prepare($query);
			$res = $statement->execute($parent_ids);
			if($res->numRows() == 0)
			{
				return $children_ids;
			}
			else
			{
				$parent_ids = array();
				while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					$parent_ids[] = $record[LearningObject :: PROPERTY_ID];
					$children_ids[] = $record[LearningObject :: PROPERTY_ID];
				}
			}
			$res->free();
		}
		while(true);
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
	static function from_db_date($date)
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
	static function to_db_date($date)
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
	 * @param boolean $additional_properties_known True if the additional
	 *                                             properties of the
	 *                                             learning object were
	 *                                             fetched.
	 * @return LearningObject The learning object.
	 */
	function record_to_learning_object($record, $additional_properties_known = false)
	{
		if (!is_array($record) || !count($record))
		{
			die('Invalid data retrieved from database');
		}
		$defaultProp = array ();
		foreach (LearningObject :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		$defaultProp[LearningObject :: PROPERTY_CREATION_DATE] = self :: from_db_date($defaultProp[LearningObject :: PROPERTY_CREATION_DATE]);
		$defaultProp[LearningObject :: PROPERTY_MODIFICATION_DATE] = self :: from_db_date($defaultProp[LearningObject :: PROPERTY_MODIFICATION_DATE]);
		if ($additional_properties_known)
		{
			$additionalProp = array ();
			$properties = $this->get_additional_properties($record[LearningObject :: PROPERTY_TYPE]);
			if (count($properties) > 0)
			{
				foreach ($properties as $prop)
				{
					$additionalProp[$prop] = $record[$prop];
				}
			}
		}
		else
		{
			$additionalProp = null;
		}
		return LearningObject :: factory($record[LearningObject :: PROPERTY_TYPE], $record[LearningObject :: PROPERTY_ID], $defaultProp, $additionalProp);
	}

	/**
	 * Translates a string with wildcard characters "?" (single character)
	 * and "*" (any character sequence) to a SQL pattern for use in a LIKE
	 * condition. Should be suitable for any SQL flavor.
	 * @param string $string The string that contains wildcard characters.
	 * @return string The escaped string.
	 */
	static function translate_search_string($string)
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
	 * @param boolean $prefix_learning_object_properties Whether or not to
	 *                                                   prefix learning
	 *                                                   object properties
	 *                                                   to avoid collisions.
	 * @return string The escaped column name.
	 */
	function escape_column_name($name, $prefix_learning_object_properties = false)
	{
		list($table, $column) = explode('.', $name, 2);
		$prefix = '';
		if (isset($column))
		{
			$prefix = $table.'.';
			$name = $column;
		}
		elseif ($prefix_learning_object_properties && self :: is_learning_object_column($name))
		{
			$prefix = self :: ALIAS_LEARNING_OBJECT_TABLE.'.';
		}
		return $prefix.$this->connection->quoteIdentifier($name);
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
		global $repository_database;
		return $repository_database.'.'.$this->prefix.$name;
	}

	/**
	 * Escapes a table name in accordance with the database type.
	 * @param string $name The table identifier.
	 * @return string The escaped table name.
	 */
	function escape_table_name($name)
	{
		global $repository_database;
		$database_name = $this->connection->quoteIdentifier($repository_database);
		return $database_name.'.'.$this->connection->quoteIdentifier($this->prefix.$name);
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
	function get_used_disk_space($owner)
	{
		$condition_owner = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, $owner);
		$types = $this->get_registered_types();
		foreach ($types as $index => $type)
		{
			$class = LearningObject :: type_to_class($type);
			$properties = call_user_func(array ($class, 'get_disk_space_properties'));
			if (is_null($properties))
			{
				continue;
			}
			if (!is_array($properties))
			{
				$properties = array ($properties);
			}
			$sum = array ();
			foreach ($properties as $index => $property)
			{
				$sum[] = 'SUM('.$this->escape_column_name($property).')';
			}
			if ($this->is_extended_type($type))
			{
				$query = 'SELECT '.implode('+', $sum).' AS disk_space FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' JOIN '.$this->escape_table_name($type).' AS '.self :: ALIAS_TYPE_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).' = '.self :: ALIAS_TYPE_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID);
				$condition = $condition_owner;
			}
			else
			{
				$query = 'SELECT '.implode('+', $sum).' AS disk_space FROM '.$this->escape_table_name('learning_object');
				$match = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
				$condition = new AndCondition(array ($match, $condition_owner));
			}
			$params = array ();
			$query .= ' WHERE '.$this->translate_condition($condition, & $params, true);
			$sth = $this->connection->prepare($query);
			$res = & $sth->execute($params);
			$record = $res->fetchRow(MDB2_FETCHMODE_OBJECT);
			$disk_space += $record->disk_space;
			$res->free();
		}
		return $disk_space;
	}

	// Inherited
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
		$manager->createTable($name,$properties);
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

	private static function is_learning_object_column ($name)
	{
		return LearningObject :: is_default_property_name($name) || $name == LearningObject :: PROPERTY_TYPE || $name == LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX || $name == LearningObject :: PROPERTY_ID;
	}
}
?>