<?php
/**
 * package migration.platform.dokeos185
 * 
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */
require_once(Path :: get_library_path().'configuration/configuration.php');
require_once(Path :: get_path(SYS_APP_MIGRATION_PATH) . '/lib/failedelement.class.php');
require_once(Path :: get_path(SYS_APP_MIGRATION_PATH) . '/lib/idreference.class.php');
require_once(Path :: get_path(SYS_APP_MIGRATION_PATH) . '/lib/recoveryelement.class.php');

//TODO use pear package for lcms database connection
abstract class MigrationDataManager
{
	abstract function validate_settings();
	abstract function move_file($old_rel_path, $new_rel_path,$filename);
	abstract function create_directory($is_new_system, $rel_path);
	abstract function append_full_path($is_new_system, $rel_path);
	
	private static $instance;
	private $db_lcms;
	
	const TEMP_FAILED_ELEMENTS_TABLE = 'temp_failed_elements';
	const TEMP_RECOVERY_TABLE = 'temp_recovery';
	const TEMP_ID_REFERENCE_TABLE = 'temp_id_reference';
	
	/**
	 * Singleton and factory pattern in one
	 */
	static function getInstance($platform, $old_directory)
	{
		if(!isset(self :: $instance))
		{
			$filename = dirname(__FILE__) . '/../platform/' . strtolower($platform) . '/' . 
				strtolower($platform) . 'datamanager.class.php';
			if (!file_exists($filename) || !is_file($filename))
			{
				echo($filename);
				die('Failed to load ' . $platform . 'datamanager.class.php');
			}
			$class = $platform . 'DataManager';
			require_once $filename;
			self :: $instance = new $class($old_directory);
		}
		
		return self :: $instance;
	}
	
	function MigrationDataManager()
	{
		$this->db_lcms_connect();
	}
	
	/**
	 * makes a connection to the LCMS database
	 */
	function db_lcms_connect()
	{
		$conf = Configuration :: get_instance();
		$this->db_lcms = MDB2 :: connect($conf->get_parameter('database', 'connection_string'),
			array('debug'=>3,'debug_handler'=>array('MigrationDataManager','debug')));
		$this->db_lcms->query('SET NAMES utf8');
	}
	
	/**
	 * gets the parent_id from a learning object
	 * 
	 * @param int $owner id of the owner of the learning object
	 * @param String $type type of the learning object
	 * @param String $title title of the learning object
	 * @return $record returns a parent_id
	 */
	function get_parent_id($owner,$type,$title)
	{
		$this->db_lcms_connect();
		$query = 'SELECT id FROM repository_learning_object WHERE owner=\'' . $owner . '\' AND type=\'' . $type .
		 		'\' AND title=\'' . $title . '\'';
	
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		return $record['id'];
	}
	
	/**
	 * creates temporary tables in the LCMS-database for the migration
	 */
	function create_temporary_tables()
	{
		$this->db_lcms_connect();
		
		$this->delete_temporary_tables();
		
		$query = 'CREATE TABLE ' . self :: TEMP_FAILED_ELEMENTS_TABLE . ' (
				  id int NOT NULL AUTO_INCREMENT,
				  failed_id varchar(50),
				  table_name varchar(50),
				  primary key(id));';
		$this->db_lcms->query($query);
		
		$query = 'CREATE TABLE ' . self :: TEMP_RECOVERY_TABLE . ' (
				  id int NOT NULL AUTO_INCREMENT,
				  old_path varchar(200),
				  new_path varchar(200),
				  primary key(id));';
		$this->db_lcms->query($query);

		$query = 'CREATE TABLE ' . self :: TEMP_ID_REFERENCE_TABLE . ' (
				  id int NOT NULL AUTO_INCREMENT,
				  old_id varchar(50),
				  new_id varchar(50),
				  table_name varchar(50),
				  primary key(id));';
		$this->db_lcms->query($query);
	}
	
	/**
	 * deletes temporary tables in the LCMS-database for the migration
	 */
	function delete_temporary_tables()
	{	
		$this->db_lcms_connect();
		$query = 'DROP TABLE IF EXISTS ' . self :: TEMP_FAILED_ELEMENTS_TABLE;
		$this->db_lcms->query($query);
		
		$query = 'DROP TABLE IF EXISTS ' . self :: TEMP_RECOVERY_TABLE;
		$this->db_lcms->query($query);
		
		$query = 'DROP TABLE IF EXISTS ' . self :: TEMP_ID_REFERENCE_TABLE;
		$this->db_lcms->query($query);
		
	}
	
	/**
	 * add a failed migration element to table failed_elements
	 * @param String $failed_id ID from the object that failed to migrate
	 * @param String $table The table where the failed_id is stored
	 */
	function add_failed_element($failed_id,$table)
	{
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_FAILED_ELEMENTS_TABLE . 
				 ' (failed_id, table_name) VALUES (\''.
					$failed_id . '\',\'' . $table.'\')';
		$this->db_lcms->query($query);
		
	}
	
	function create_failed_element($failed_element)
	{
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_FAILED_ELEMENTS_TABLE . 
				 ' (failed_id, table_name) VALUES (\''.
					$failed_element->get_failed_id() . '\',\'' . 
					$failed_element->get_table_name() .'\')';
		$this->db_lcms->query($query);
		
	}
	
	/**
	 * add a migrated file to the table recovery to make a rollback action possible
	 * @param String $old_path the old path of an element
	 * @param String $new_path the new path of an element
	 */
	function add_recovery_element($old_path,$new_path)
	{
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_RECOVERY_TABLE .
				 '(old_path, new_path) VALUES (\''.
					$old_path . '\',\''.$new_path .'\')';
		$this->db_lcms->query($query);
	}
	
	function create_recovery_element($recovery_element)
	{
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_RECOVERY_TABLE .
				 '(old_path, new_path) VALUES (\''.
					$recovery_element->get_old_path() . '\',\'' . 
					$recovery_element->get_new_path() .'\')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * add an id reference to the table id_reference
	 * @param String $old_id The old ID of an element
	 * @param String $new_id The new ID of an element
	 * @param String $table_name The name of the table where an element is placed
	 */
	function add_id_reference($old_id,$new_id,$table_name)
	{	
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_ID_REFERENCE_TABLE . 
				 ' (old_id, new_id, table_name) VALUES (\'' .
					$old_id . '\',\'' . $new_id . '\',\'' . $table_name . '\')';
		$this->db_lcms->query($query);
	}
	
	function create_id_reference($id_reference)
	{	
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_ID_REFERENCE_TABLE . 
				 ' (old_id, new_id, table_name) VALUES (\'' .
					$id_reference->get_old_id() . '\',\'' . 
					$id_reference->get_new_id() . '\',\'' . 
					$id_reference->get_table_name() . '\')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * select an failed migration element from table failed_elements by id
	 * @param int $id ID of  an failed migration element
	 * @return database-record failed migration record
	 */
	 function get_failed_element($table_name, $old_id)
	 {	
		$this->db_lcms_connect();
	 	$query = 'SELECT * FROM ' . self :: TEMP_FAILED_ELEMENTS_TABLE . 
				 ' WHERE table_name=\'' . $table_name . '\' AND failed_id=\'' . $old_id . '\'';
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
	
		
		if($record)
			return $record;
			
		return NULL;
	 }
	 
	 function get_failed_elements($table_name)
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT * FROM ' . self :: TEMP_FAILED_ELEMENTS_TABLE . 
				 ' WHERE table_name=\'' . $table_name . '\'';
		$result = $this->db_lcms->query($query);
		$failed_elements = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$failed_elements = $this->record_to_classobject($record, 'FailedElement');
		}
		
		$result->free();
		
		return $failed_elements;
	 }
	 
	 function get_recovery_elements()
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT * FROM ' . self :: TEMP_RECOVERY_TABLE;
		$result = $this->db_lcms->query($query);
		$recovery_elements = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$recovery_elements[] = $this->record_to_classobject($record, 'RecoveryElement');
		}
		
		$result->free();
		
		return $recovery_elements;
	 }
	 
	 function get_id_references($table_name)
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT new_id FROM ' . self :: TEMP_ID_REFERENCE_TABLE . 
				 ' WHERE table_name=\'' . $table_name . '\'';
		$result = $this->db_lcms->query($query);
		$id_references = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$id_references[] = $this->record_to_classobject($record, 'IdReference');
		}
		
		$result->free();
		
		return $id_references;
	 }
	 
	 function record_to_classobject($record, $classname)
	 {
		 if (!is_array($record) || !count($record))
		 {
		 	throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		 }
		 $defaultProp = array ();
		 
		 $class = new $classname($defaultProp);
		 
		 foreach ($class->get_default_property_names() as $prop)
		 {
		 	 $defaultProp[$prop] = $record[$prop];
		 }
		 
		 $class->set_default_properties($defaultProp);
		 
		 return $class;
	 }
	 
	 /**
	 * select a recovery element from table recovery by id
	 * @param int $id ID of  an recovery element
	 * @return database-record recovery record
	 */
	 function get_recovery_element($id)
	 {	
		$this->db_lcms_connect();
	 	$query = 'SELECT * FROM ' . self :: TEMP_RECOVERY_TABLE . 
				 ' WHERE id = \'' . $id . '\'';
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		if($record)
			return $record;
		
		return NULL;
	 }
	 
	/**
	 * select an id reference element from table id_reference by id
	 * @param int $id ID of  an id_reference element
	 * @return database-record id_reference record
	 */
	 function get_id_reference($old_id, $table_name)
	 {
		$this->db_lcms_connect();
	 	$query = 'SELECT new_id FROM ' . self :: TEMP_ID_REFERENCE_TABLE . 
				 ' WHERE old_id = \'' . $old_id . '\' AND table_name=\'' . $table_name . '\'';
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		if($record)
			return $record['new_id'];
			
		return NULL;
	 }
	 
	 /**
	  * Checks if an authentication method is available in the lcms system
	  * @param string $auth_method Authentication method to check for
	  * @return true if method is available
	  */
	 function is_authentication_available($auth_method)
	 {
	 	//TODO: make a authentication method list
	 	return true;
	 }
	 
	 /**
	  * Checks if a language is available in the lcms system
	  * @param string $language Language to check for
	  * @return true if language is available
	  */
	 function is_language_available($language)
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT id FROM admin_language WHERE folder=\'' . $language . '\';';

	 	$result = $this->db_lcms->query($query);
	 	return ($result->numRows() > 0);
	 }

	 
	 /**
	  * get the next position
	  * @return int next position
	  */
	 function get_next_position($table_name,$field_name)
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT MAX(' . $field_name . ') AS \'highest\' FROM ' . $table_name;
	 	
	 	$result = $this->db_lcms->query($query);
	 	$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
	 	$number = $record['highest'];
	 	
	 	return ++$number;
	 }

	 
	 /**
	  * Checks if a code is allready available in a table
	  */
	 function code_available($table_name, $code)
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT * FROM ' . $table_name . ' WHERE code=\'' . $code . '\'';
	 	$result = $this->db_lcms->query($query);
	 	return ($result->numRows() > 0);
	 }
	 
	/**
	 * Creates a unix time from the given timestamp
	 */
	function make_unix_time($date) 
	{
		list($dat, $tim) = explode(" ", $date);
		list($y, $mo, $d) = explode("-", $dat);
		list($h, $mi, $s) = explode(":", $tim);
	
		return mktime($h, $mi, $s, $mo, $d, $y);
	}
}

?>
