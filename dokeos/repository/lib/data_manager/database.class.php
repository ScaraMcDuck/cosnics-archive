<?php
/**
 * @package repository
 * @subpackage datamanager
 */
require_once dirname(__FILE__).'/database/database_learning_object_result_set.class.php';
require_once dirname(__FILE__).'/database/database_complex_learning_object_item_result_set.class.php';
require_once dirname(__FILE__).'/../repository_data_manager.class.php';
require_once Path :: get_library_path().'configuration/configuration.class.php';
require_once dirname(__FILE__).'/../learning_object.class.php';
require_once dirname(__FILE__).'/../complex_learning_object_item.class.php';
require_once Path :: get_library_path().'condition/condition_translator.class.php';
require_once Path :: get_admin_path().'lib/admin_data_manager.class.php';

require_once 'MDB2.php';

/**
==============================================================================
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *	@author Tim De Pauw
 *	@author Bart Mollet
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
==============================================================================
 */

class DatabaseRepositoryDataManager extends RepositoryDataManager
{
	const ALIAS_LEARNING_OBJECT_TABLE = 'lo';
	const ALIAS_LEARNING_OBJECT_VERSION_TABLE = 'lov';
	const ALIAS_LEARNING_OBJECT_ATTACHMENT_TABLE = 'loa';
	const ALIAS_TYPE_TABLE = 'tt';
	const ALIAS_LEARNING_OBJECT_PARENT_TABLE = 'lop';
	const ALIAS_COMPLEX_LEARNING_OBJECT_ITEM_TABLE = 'cloi';

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

		$this->connection = Connection :: get_instance()->get_connection();
		$this->connection->setOption('debug_handler', array(get_class($this),'debug'));

		if (PEAR::isError($this)) {
   		 die($this->connection->getMessage());
		}
		$this->prefix = 'repository_';
		$this->connection->query('SET NAMES utf8');
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
//			echo '<pre>';
//		 	echo($args[2]);
//		 	echo '</pre>';
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
		$query .= ' JOIN ' . $this->escape_table_name('learning_object_version') . ' AS ' . self :: ALIAS_LEARNING_OBJECT_VERSION_TABLE . ' ON ' . self :: ALIAS_LEARNING_OBJECT_TABLE . '.' . LearningObject :: PROPERTY_ID . ' = ' . self :: ALIAS_LEARNING_OBJECT_VERSION_TABLE . '.' . LearningObject :: PROPERTY_ID;

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
		$query = 'SELECT '.implode(',', array_map(array($this, 'escape_column_name'), $learning_object->get_additional_property_names())).' FROM '.$this->escape_table_name($type).' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_ID).'=?';
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
				$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' JOIN '.$this->escape_table_name($type).' AS '.self :: ALIAS_TYPE_TABLE.' ON '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).' = '.self :: ALIAS_TYPE_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID);
			}
			else
			{
				$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE;
				$match = new EqualityCondition(LearningObject :: PROPERTY_TYPE, $type);
				$condition = isset ($condition) ? new AndCondition(array ($match, $condition)) : $match;
			}
		}
		else
		{
			$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE;
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
		$query .= ' JOIN ' . $this->escape_table_name('learning_object_version') . ' AS ' . self :: ALIAS_LEARNING_OBJECT_VERSION_TABLE . ' ON ' . self :: ALIAS_LEARNING_OBJECT_TABLE . '.' . LearningObject :: PROPERTY_ID . ' = ' . self :: ALIAS_LEARNING_OBJECT_VERSION_TABLE . '.' . LearningObject :: PROPERTY_ID;


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
		$res->free();
		return $record[0];
	}

	// Inherited
	function count_learning_object_versions($object)
	{
		$query = 'SELECT COUNT('.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_ID).') FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' WHERE '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($object->get_object_number());
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

	function get_next_learning_object_number()
	{
		$id = $this->connection->nextID($this->get_table_name('learning_object') .'_number');
		return $id;
	}

	// Inherited.
	function create_learning_object($object, $type)
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

		$props = array();
		$props[$this->escape_column_name(LearningObject :: PROPERTY_ID)] = $object->get_id();
		if ($type == 'new')
		{
			$props[$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER)] = $object->get_object_number();
		  	$this->connection->extended->autoExecute($this->get_table_name('learning_object_version'), $props, MDB2_AUTOQUERY_INSERT);
		}
		elseif($type == 'version')
		{
		  	$this->connection->extended->autoExecute($this->get_table_name('learning_object_version'), $props, MDB2_AUTOQUERY_UPDATE, $this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER) . '=' .$object->get_object_number());
		}
		else
		{
			return false;
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

	//Inherited.
	function retrieve_learning_object_by_user($user_id)
	{
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_OWNER_ID).'=?';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($user_id);
		return new DatabaseLearningObjectResultSet($this, $res, isset($type));
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

			// Delete entry in version table
			$query = 'DELETE FROM '.$this->escape_table_name('learning_object_version').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).'=?';
			$statement = $this->connection->prepare($query);
			$statement->execute($object->get_object_number());

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
	function delete_learning_object_version($object)
	{
		if( !$this->learning_object_deletion_allowed($object, 'version'))
		{
			return false;
		}

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

		if ($object->is_latest_version())
		{
			$object_number = $object->get_object_number();
			$query = 'SELECT * FROM '.$this->escape_table_name('learning_object').' AS '.self :: ALIAS_LEARNING_OBJECT_TABLE.' WHERE '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).'=? ORDER BY '.self :: ALIAS_LEARNING_OBJECT_TABLE.'.'. $this->escape_column_name(LearningObject :: PROPERTY_ID) .' DESC';
			$this->connection->setLimit(1);
			$statement = $this->connection->prepare($query);
			$res = $statement->execute($object_number);
			$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();

			$props = array();
			$props[$this->escape_column_name(LearningObject :: PROPERTY_ID)] = $record['id'];
			$this->connection->loadModule('Extended');
			$this->connection->extended->autoExecute($this->get_table_name('learning_object_version'), $props, MDB2_AUTOQUERY_UPDATE, $this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER) . '=' .$object_number);
		}

		return true;
	}

	// Inherited.
	function delete_learning_object_attachments($object)
	{
		// TODO: SCARA - Add notification for users who are using this object as an attachment
//		$subject = '['.PlatformSetting :: get('site_name').'] '.$publication->get_learning_object()->get_title();
//		// TODO: SCARA - Add meaningfull attachment removal message
//		$body = 'message';
//		$user = $object->get_owner_id();
//		$mail = Mail :: factory($subject, $body, $user->get_email());
//		$mail->send();

		// Delete all attachments (only the links, not the actual objects)
		$query = 'DELETE FROM '.$this->escape_table_name('learning_object_attachment').' WHERE '.$this->escape_column_name('attachment').'=?';
		$sth = $this->connection->prepare($query);

		if ($sth->execute($object->get_id()))
		{
			return true;
		}
		else
		{
			return false;
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
				$sth->execute();
			}
		}
		$sth = $this->connection->prepare('DELETE FROM '.$this->escape_table_name('learning_object'));
		$sth->execute();
	}

	function is_latest_version($object)
	{
		$query = 'SELECT '. LearningObject :: PROPERTY_ID .' FROM ' . $this->escape_table_name('learning_object_version') . ' WHERE ' . LearningObject :: PROPERTY_OBJECT_NUMBER . '=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($object->get_object_number());
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		if ($record['id'] == $object->get_id())
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function is_only_document_occurence($path)
	{
		$query = 'SELECT COUNT('. LearningObject :: PROPERTY_ID .') AS ids FROM ' . $this->escape_table_name('document') . ' WHERE path=?';
		//$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($path);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

		if ($record['ids'] == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
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

	function retrieve_learning_object_versions ($object)
	{
		$object_number = $object->get_object_number();
		$query = 'SELECT '.$this->escape_column_name(LearningObject :: PROPERTY_ID).' FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).'=? AND '.$this->escape_column_name(LearningObject :: PROPERTY_STATE).'=?';
		$sth = $this->connection->prepare($query);
		$res = $sth->execute(array($object_number, $object->get_state()));
		$attachments = array();
		while ($record = $res->fetchRow(MDB2_FETCHMODE_ORDERED))
		{
			$versions[] = $this->retrieve_learning_object($record[0]);
		}
		$res->free();
		return $versions;
	}

	function get_latest_version_id ($object)
	{
		$object_number = $object->get_object_number();
		$query = 'SELECT * FROM '.$this->escape_table_name('learning_object_version').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).'=?';
		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($object_number);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();

		return $record['id'];
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

	function get_version_ids($object)
	{
		$version_ids = array();
		$query = 'SELECT '.$this->escape_column_name(LearningObject :: PROPERTY_ID).' FROM '.$this->escape_table_name('learning_object').' WHERE '.$this->escape_column_name(LearningObject :: PROPERTY_OBJECT_NUMBER).' =? ORDER BY '.$this->escape_column_name(LearningObject :: PROPERTY_ID).' ASC';
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($object->get_object_number());

		while($record = $res->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$version_ids[] = $record[LearningObject :: PROPERTY_ID];
		}
		return $version_ids;
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
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (LearningObject :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		$defaultProp[LearningObject :: PROPERTY_CREATION_DATE] = self :: from_db_date($defaultProp[LearningObject :: PROPERTY_CREATION_DATE]);
		$defaultProp[LearningObject :: PROPERTY_MODIFICATION_DATE] = self :: from_db_date($defaultProp[LearningObject :: PROPERTY_MODIFICATION_DATE]);
		
		$learning_object = LearningObject :: factory($record[LearningObject :: PROPERTY_TYPE], $record[LearningObject :: PROPERTY_ID], $defaultProp);
		
		if ($additional_properties_known)
		{
			$properties = $learning_object->get_additional_property_names();
			
			$additionalProp = array ();
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
		
		$learning_object->set_additional_properties($additionalProp);
		
		return $learning_object;
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
		$dsn = $this->connection->getDSN('array');
		return $dsn['database'].'.'.$this->prefix.$name;
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
			if (isset ($condition))
			{
				$translator = new ConditionTranslator($this, $params, $prefix_properties = true);
				$translator->translate($condition);
				$query .= $translator->render_query();
			}
			$params = $translator->get_parameters();

			$sth = $this->connection->prepare($query);
			$res = $sth->execute($params);
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

	private static function is_learning_object_column ($name)
	{
		return LearningObject :: is_default_property_name($name) || $name == LearningObject :: PROPERTY_TYPE || $name == LearningObject :: PROPERTY_DISPLAY_ORDER_INDEX || $name == LearningObject :: PROPERTY_ID;
	}

	function ExecuteQuery($sql)
	{
		$this->connection->query($sql);
	}

	function is_attached ($object, $type = null)
	{
		$query = 'SELECT COUNT('.$this->escape_column_name("learning_object").') FROM '.$this->escape_table_name('learning_object_attachment').' AS '.self :: ALIAS_LEARNING_OBJECT_ATTACHMENT_TABLE .' WHERE '. self :: ALIAS_LEARNING_OBJECT_ATTACHMENT_TABLE . '.attachment';
		if (isset($type))
		{
			$query.= '=?';
			$params = $object->get_id();
		}
		else
		{
			$query.= ' IN (?'.str_repeat(',?', count($this->get_version_ids($object)) - 1).')';
			$params = $this->get_version_ids($object);
		}
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		if ($record[0] > 0)
		{
		  return true;
		}
		else
		{
		  return false;
		}
	}


	/**
	 * Returns the next available complex learning object ID.
	 * @return int The ID.
	 */
	function get_next_complex_learning_object_item_id()
	{
		$id = $this->connection->nextID($this->get_table_name('complex_learning_object'));
		return $id;
	}

	/**
	 * Creates a new complex learning object in the database
	 * @param ComplexLearningObject $clo - The complex learning object
	 * @return True if success
	 */
	function create_complex_learning_object_item($clo_item)
	{
		$props = array();
		foreach ($clo_item->get_default_properties() as $key => $value)
		{ 
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('complex_learning_object_item'), $props, MDB2_AUTOQUERY_INSERT);
		if ($clo_item->is_extended())
		{
			if($clo_item->is_complex_ref())
			{
				$ref_item = $this->retrieve_complex_learning_object_item($clo_item->get_ref());
				$ref = $ref_item->get_ref();
			}
			else
				$ref = $clo_item->get_ref();
			
			$props = array();
			foreach ($clo_item->get_additional_properties() as $key => $value)
			{
				$props[$this->escape_column_name($key)] = $value;
			}
			$props[$this->escape_column_name(ComplexLearningObjectItem :: PROPERTY_ID)] = $clo_item->get_id();
			$type = $this->determine_learning_object_type($ref);
			$this->connection->extended->autoExecute($this->get_table_name('complex_' . $type), $props, MDB2_AUTOQUERY_INSERT);
		}

		return true;
	}

	/**
	 * Updates a complex learning object in the database
	 * @param ComplexLearningObject $clo - The complex learning object
	 * @return True if success
	 */
	function update_complex_learning_object_item($clo_item)
	{
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_ID, $clo_item->get_id());

		$props = array();
		foreach ($clo_item->get_default_properties() as $key => $value)
		{
			if($key == ComplexLearningObjectItem :: PROPERTY_ID) continue;
			$props[$this->escape_column_name($key)] = $value;
		}
		$this->connection->loadModule('Extended');
		$this->connection->extended->autoExecute($this->get_table_name('complex_learning_object_item'), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		if ($clo_item->is_extended() && !$clo_item->is_complex_ref())
		{
			if($clo_item->is_complex_ref())
			{
				$ref_item = $this->retrieve_complex_learning_object_item($clo_item->get_ref());
				$ref = $ref_item->get_ref();
			}
			else
				$ref = $clo_item->get_ref();
			
			$props = array();
			foreach ($clo_item->get_additional_properties() as $key => $value)
			{
				$props[$this->escape_column_name($key)] = $value;
			}
			$type = $this->determine_learning_object_type($ref);
			$this->connection->extended->autoExecute($this->get_table_name('complex_' . $type), $props, MDB2_AUTOQUERY_UPDATE, $condition);
		}
		return true;
	}

	/**
	 * Deletes a complex learning object in the database
	 * @param ComplexLearningObject $clo - The complex learning object
	 * @return True if success
	 */
	function delete_complex_learning_object_item($clo_item)
	{
		/*if($this->learning_object_is_published($clo_item->get_id()))
		{
			return false;
		}*/

		$query = 'DELETE FROM '.$this->escape_table_name('complex_learning_object_item');
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_ID, $clo_item->get_id());

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		//$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);

		if ($clo_item->is_extended())
		{
			if($clo_item->is_complex_ref())
			{
				$ref_item = $this->retrieve_complex_learning_object_item($clo_item->get_ref());
				$ref = $ref_item->get_ref();
			}
			else
				$ref = $clo_item->get_ref();
			
			$type = $this->determine_learning_object_type($ref);
			$query = 'DELETE FROM '.$this->get_table_name('complex_' . $type);

			$params = array ();
			if (isset ($condition))
			{
				$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
				$translator->translate($condition);
				$query .= $translator->render_query();
				$params = $translator->get_parameters();
			}

			//$this->connection->setLimit(1);
			$statement = $this->connection->prepare($query);
			$res = $statement->execute($params);
		}

		/*$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $clo_item->get_id());
		$items = $this->retrieve_complex_learning_object_items($condition);

		foreach($items as $item)
		{
			$this->delete_complex_learning_object_item($item);
		}*/

		return true;

	}

	/**
	 * Retrieves a complex learning object from the database with a given id
	 * @param Int $clo_id
	 * @return The complex learning object
	 */
	function retrieve_complex_learning_object_item($clo_item_id)
	{
		// Retrieve main table

		$query = 'SELECT * FROM '.$this->escape_table_name('complex_learning_object_item').' AS '.
				 self :: ALIAS_COMPLEX_LEARNING_OBJECT_ITEM_TABLE;

		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_ID, $clo_item_id);

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$this->connection->setLimit(1);
		$statement = $this->connection->prepare($query);
		$res = $statement->execute($params);
		$rec1 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$res->free();

		// Determine type

		$is_complex_ref = $rec1[ComplexLearningObjectItem :: PROPERTY_COMPLEX_REF];
		if($is_complex_ref)
		{
			$item = $this->retrieve_complex_learning_object_item($rec1[ComplexLearningObjectItem :: PROPERTY_REF]);
			$ref = $item->get_ref();
		}
		else
			$ref = $rec1[ComplexLearningObjectItem :: PROPERTY_REF];

		$type = $this->determine_learning_object_type($ref);
		$cloi = ComplexLearningObjectItem :: factory($type, array(), array());
		
		$bool = false;
		$rec2 = array();
		
		if($cloi->is_extended())
		{
		
			// Retrieve extended table
	
			$query = 'SELECT * FROM '.$this->escape_table_name('complex_' . $type).' AS '.
					 self :: ALIAS_TYPE_TABLE;
	
			$params = array ();
			if (isset ($condition))
			{
				$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
				$translator->translate($condition);
				$query .= $translator->render_query();
				$params = $translator->get_parameters();
			}
	
			$this->connection->setLimit(1);
			$statement = $this->connection->prepare($query);
			$res = $statement->execute($clo_item_id);
			$rec2 = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			$res->free();

			$bool = true;
		}
		
		$record = array_merge($rec1, $rec2);
		
		return self :: record_to_complex_learning_object_item($record, $type, $bool);
	}

	/**
	 * Mapper for a record to a complex learning object item
	 * @param Record $record
	 * @return ComplexLearningObjectItem
	 */
	function record_to_complex_learning_object_item($record, $type = null, $additional_properties_known = false)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}
		
		$cloi = ComplexLearningObjectItem :: factory($type, array(), array());
		
		$defaultProp = array ();
		foreach ($cloi->get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		$cloi->set_default_properties($defaultProp);

		if ($additional_properties_known && $type)
		{
			$additionalProp = array ();
			foreach ($cloi->get_additional_property_names() as $prop)
			{
				$additionalProp[$prop] = $record[$prop];
			}
			$cloi->set_additional_properties($additionalProp);
		}
		else
		{
			$additionalProp = null;
		}

		return $cloi;
	}

	/**
	 * Counts the available complex learning objects with the given condition
	 * @param Condition $condition
	 * @return Int the amount of complex learning objects
	 */
	function count_complex_learning_object_items($condition)
	{
		$query = 'SELECT COUNT('.self :: ALIAS_COMPLEX_LEARNING_OBJECT_ITEM_TABLE.'.'.
				 $this->escape_column_name(ComplexLearningObjectItem :: PROPERTY_ID).') FROM '.
				 $this->escape_table_name('complex_learning_object_item').' AS '.
				 self :: ALIAS_COMPLEX_LEARNING_OBJECT_ITEM_TABLE;
				 
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
		return $record[0];
	}

	/**
	 * Retrieves the complex learning object items with the given condition
	 * @param Condition
	 */
	function retrieve_complex_learning_object_items($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1)
	{
		$query = 'SELECT * FROM ' . $this->escape_table_name('complex_learning_object_item') . ' AS ' .
				 self :: ALIAS_COMPLEX_LEARNING_OBJECT_ITEM_TABLE;

		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}

		$orderBy[] = ComplexLearningObjectItem :: PROPERTY_DISPLAY_ORDER;
		$orderDir[] = SORT_ASC;
		$order = array ();

		for ($i = 0; $i < count($orderBy); $i ++)
		{
			$order[] = $this->escape_column_name($orderBy[$i], false).' '. ($orderDir[$i] == SORT_DESC ? 'DESC' : 'ASC');
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

		return new DatabaseComplexLearningObjectItemResultSet($this, $res, true);
	}

	function select_next_display_order($parent_id)
	{
		$query = 'SELECT MAX(' . ComplexLearningObjectItem :: PROPERTY_DISPLAY_ORDER . ') AS do FROM ' . 
			$this->escape_table_name('complex_learning_object_item');
	
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $parent_id);
		
		$params = array ();
		if (isset ($condition))
		{
			$translator = new ConditionTranslator($this, $params, $prefix_properties = false);
			$translator->translate($condition);
			$query .= $translator->render_query();
			$params = $translator->get_parameters();
		}
		
		$sth = $this->connection->prepare($query);
		$res = $sth->execute($params);
		$record = $res->fetchRow(MDB2_FETCHMODE_ORDERED);
		$res->free();
	
		return $record[0] + 1;
	}

}
?>
