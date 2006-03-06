<?php
require_once dirname(__FILE__) . '/../datamanager.class.php';
require_once dirname(__FILE__) . '/../configuration.class.php';
require_once dirname(__FILE__) . '/../learningobject.class.php';
require_once 'DB.php';

/**
==============================================================================
*	This is a data manager that uses a database for storage. It was written
*	for MySQL, but should be compatible with most SQL flavors.
==============================================================================
*/
class DatabaseDataManager extends DataManager {
	/**
	 * The database connection.
	 */
	private $connection;

	/**
	 * The table name prefix, if any.
	 */
	private $prefix;

	// Inherited.
    function initialize () {
		PEAR::setErrorHandling(PEAR_ERROR_CALLBACK,
			array(get_class(), 'handle_error'));
    	$conf = Configuration::get_instance();
    	$this->connection = DB::connect(
    		$conf->get_parameter('database', 'connection_string'));
    	$this->prefix = $conf->get_parameter('database', 'table_name_prefix');
    }

    // Inherited.
    function determine_learning_object_type ($id) {
    	$sth = $this->connection->prepare(
			'SELECT `type` FROM `'
			. $this->prefix . 'learning_object` WHERE `id`=? LIMIT 1');
		$res =& $this->connection->execute($sth, $id);
		$row = $res->fetchRow(DB_FETCHMODE_ORDERED);
		return $row[0];
    }

    // Inherited.
    function retrieve_learning_object ($id, $type = null) {
    	if (is_null($type)) {
    		$type = $this->determine_learning_object_type($id);
    	}
    	$sth = $this->connection->prepare(
			'SELECT * FROM `'
			. $this->prefix . 'learning_object` AS `l`'
    		. ' JOIN `' . $this->prefix . $type
    		. '` AS `a` ON `l`.`id`=`a`.`id`'
    		. ' WHERE `l`.`id`=? LIMIT 1');
    	$res =& $this->connection->execute($sth, $id);
		$row = $res->fetchRow(DB_FETCHMODE_ASSOC);
		return self::record_to_learning_object($row);
    }

    // Inherited.
    function retrieve_learning_objects
   	($properties = array(), $propertiesPartial = array(),
	$orderBy = array(), $orderDesc = array()) {
		$query = 'SELECT `id`, `type` FROM `'
			. $this->prefix . 'learning_object`';
		$where = array();
		$params = array();
		foreach ($properties as $p => $v) {
			  $where[] = '`' . $p . '`=?';
			  $params[] = $v;
		}
		foreach ($propertiesPartial as $p => $v) {
			  $where[] = '`' . $p . '` LIKE ?';
			  $params[] = '%' . $this->connection->escapeSimple($v) . '%';
		}
		if (count($where)) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}
		$order = array();
		for ($i = 0; $i < count($orderBy); $i++) {
			$order[] = '`' . $orderBy[$i] . '` '
				. ($orderDesc[$i] ? 'DESC' : 'ASC');
		}
		if (count($order)) {
			$query .= 'ORDER BY ' . implode(', ', $order);
		}
    	$sth = $this->connection->prepare($query);
    	$res =& $this->connection->execute($sth, $params);
    	$objects = array();
		while ($row = $res->fetchRow(DB_FETCHMODE_ASSOC)) {
			$objects[] = $this->retrieve_learning_object(
				$row['id'], $row['type']);
		}
		return $objects;
    }

    // Inherited.
    function create_learning_object ($object) {
    	$id = $this->connection->nextId($this->prefix . 'learning_object');
    	$props = $object->get_default_properties();
		$props['id'] = $id;
		$props['type'] = $object->get_type();
		$props['created'] = self::to_db_date($props['created']);
		$props['modified'] = self::to_db_date($props['modified']);
		$this->connection->autoExecute(
			$this->prefix . 'learning_object',
			$props,
			DB_AUTOQUERY_INSERT
		);
    	if ($object->is_extended()) {
    		$props = $object->get_additional_properties();
    		$props['id'] = $id;
    		$this->connection->autoExecute(
    			$this->prefix . $object->get_type(),
    			$props,
    			DB_AUTOQUERY_INSERT
    		);
    	}
    	return $id;
    }

    // Inherited.
    function update_learning_object ($object) {
    	$where = '`id`=' . $object->get_id();
    	$props = $object->get_default_properties();
		$props['created'] = self::to_db_date($props['created']);
		$props['modified'] = self::to_db_date($props['modified']);
		$this->connection->autoExecute(
			$this->prefix . 'learning_object',
			$props,
			DB_AUTOQUERY_UPDATE,
			$where
		);
    	if ($object->is_extended()) {
    		$this->connection->autoExecute(
    			$this->prefix . $object->get_type(),
    			$object->get_additional_properties(),
    			DB_AUTOQUERY_UPDATE,
    			$where
    		);
    	}
    }

    // Inherited.
    function delete_learning_object ($object) {
    	$sth = $this->connection->prepare(
			'DELETE FROM `'
			. $this->prefix
			. 'learning_object` WHERE `id`=?');
    	$this->connection->execute($sth, $object->get_id());
    	if ($object->is_extended()) {
	    	$sth = $this->connection->prepare(
				'DELETE FROM `'
				. $this->prefix . $object->get_type() . '` WHERE `id`=?');
	    	$this->connection->execute($sth, $object->get_id());
    	}
    }

    /**
     * Handles PEAR errors. If an error is encountered, the program dies with
     * a descriptive error message.
     * @param DB_Error $error The error object.
     */
	static function handle_error ($error) {
		die(__FILE__ . ':' . __LINE__ . ': '
			. $error->getMessage()
			// For debugging only. May create a security hazard.
			//. ' (' . $error->getDebugInfo() . ')'
			);
	}

	/**
	 * Converts a datetime value (as retrieved from the database) to a UNIX
	 * timestamp (as returned by time()).
	 * @param string $date The date as a UNIX timestamp.
	 * @return int The date as a UNIX timestamp.
	 */
	private static function from_db_date ($date) {
		if (isset($date)) {
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
	private static function to_db_date ($date) {
		if (isset($date)) {
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
    private function record_to_learning_object ($record) {
		$defaultProp = array();
		foreach (LearningObject::$DEFAULT_PROPERTIES as $prop) {
			$defaultProp[$prop] = $record[$prop];
		}
		$defaultProp['created'] = self::from_db_date($defaultProp['created']);
		$defaultProp['modified'] = self::from_db_date($defaultProp['modified']);
		$additionalProp = array();
		$properties = $this->get_additional_properties($record['type']);
		if (count($properties) > 0) {
			foreach ($properties as $prop) {
				$additionalProp[$prop] = $record[$prop];
			}
		}
		return $this->factory($record['type'], $record['id'],
			$defaultProp, $additionalProp);
    }
}
?>