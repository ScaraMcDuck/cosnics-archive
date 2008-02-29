<?php
/**
 * package migration.platform.dokeos185
 * 
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */
require_once(dirname(__FILE__) . '/../../common/configuration/configuration.php');

abstract class MigrationDataManager
{
	abstract function validate_settings($parameters);
	abstract function move_file($old_rel_path, $new_rel_path,$filename);
	abstract function create_directory($is_new_system, $rel_path);
	
	private static $instances = array();
	private $db_lcms;
	
	/**
	 * Singleton and factory pattern in one
	 */
	static function getInstance($platform)
	{
		if(!isset(self :: $instances[$platform]))
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
			self :: $instances[$platform] = new $class();
		}
		
		return self :: $instances[$platform];
	}
	
	/**
	 * makes a connection to the LCMS database
	 */
	function db_lcms_connect()
	{
		$dsn = $configuration['general']['root_web'];
		$this->db_lcms = MDB2 :: connect($dsn);
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
		$query = 'SELECT id FROM repository_learning_object WHERE owner = ' . $owner . ' AND type = ' . $type .
		 		' AND title = ' . $title;
		$result = $this->db_lcms->query($query);
		$record = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		return $record;
	}
	
	/**
	 * creates temporary tables in the LCMS-database for the migration
	 */
	function create_temporary_tables()
	{
		$this->db_lcms_connect();
		$query = 'CREATE TABLE failed_elements(
				  id int identity(1,1),
				  failed_id varchar(20),
				  table_name varchar(50),
				  primary key(id))';
		$this->db_lcms->query($query);
		
		$query = 'CREATE TABLE recovery(
				  id int identity(1,1),
				  old_path varchar(200),
				  new_path varchar(200),
				  primary key(id))';
		$this->db_lcms->query($query);
		
		$query = 'CREATE TABLE id_reference(
				  id int identity(1,1),
				  old_id varchar(20),
				  new_id varchar(20),
				  table_name varchar(50),
				  primary key(id))';
		$this->db_lcms->query($query);
	}
	
	/**
	 * deletes temporary tables in the LCMS-database for the migration
	 */
	function delete_temporary_tables()
	{
		$this->db_lcms_connect();
		
		$query = 'DROP TABLE failed_elements';
		$this->db_lcms->query($query);
		
		$query = 'DROP TABLE recovery';
		$this->db_lcms->query($query);
		
		$query = 'DROP TABLE id_reference';
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
	
		$query = 'INSERT INTO failed_elements(failed_id, table_name) VALUES (\''.
					$failed_id . '\',\'' . $table.'\')';
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
		
		$query = 'INSERT INTO recovery(old_path, new_path) VALUES (\''.
					$old_path . '\',\''.$new_path .'\')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * add an id reference to the table id_reference
	 * @param String $old_id The old ID of an element
	 * @param String $new_id The new ID of an element
	 * @param String $table_name The name of the table where an element is placed
	 */
	function add_id_reference_element($old_id,$new_id,$table_name)
	{
		$this->db_lcms_connect();		

		$query = 'INSERT INTO id_reference(old_id, new_id, table_name) VALUES (\'' .
					$old_id . '\',\'' . $new_id . '\',\'' . $table_name . '\')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * select an failed migration element from table failed_elements by id
	 * @param int $id ID of  an failed migration element
	 * @return database-record failed migration record
	 */
	 function select_failed_element($id)
	 {
	 	$this->db_lcms_connect();	
	 
	 	$query = 'SELECT * FROM failed_elements WHERE id = \'' . $id . '\'';
	 	
		$result = $this->db_lcms->query($query);
		
		$record = $result;
		
		$result->free();
		
		return $record;
	 }
	 
	 /**
	 * select a recovery element from table recovery by id
	 * @param int $id ID of  an recovery element
	 * @return database-record recovery record
	 */
	 function select_recovery_element($id)
	 {
		$this->db_lcms_connect(); 	
	
	 	$query = 'SELECT * FROM recovery WHERE id = \'' . $id . '\'';
	 	
		$result = $this->db_lcms->query($query);
		
		$record = $result;
		
		$result->free();
		
		return $record;
	 }
	 
	/**
	 * select an id reference element from table id_reference by id
	 * @param int $id ID of  an id_reference element
	 * @return database-record id_reference record
	 */
	 function select_id_reference_element($id)
	 {
	 	$this->db_lcms_connect();	
	 
	 	$query = 'SELECT * FROM id_reference WHERE id = \'' . $id . '\'';
	 	
		$result = $this->db_lcms->query($query);
		
		$record = $result;
		
		$result->free();
		
		return $record;
	 }
	 
	 
}

?>
