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
	 * creates additional tables in the LCMS-database for the migration
	 */
	function create_additional_tables()
	{
		$query = 'CREATE TABLE failed_elements(
				  id int identity(1,1),
				  failed_id int,
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
				  old_id int,
				  new_id int,
				  table_name varchar(50),
				  primary key(id))';
		$this->db_lcms->query($query);
	}
	
	/**
	 * deletes additional tables in the LCMS-database for the migration
	 */
	function delete_additional_tables()
	{
		$query = 'DROP TABLE failed_elements';
		$this->db_lcms->query($query);
		
		$query = 'DROP TABLE recovery';
		$this->db_lcms->query($query);
		
		$query = 'DROP TABLE id_reference';
		$this->db_lcms->query($query);
	}
	
	/**
	 * add a failed migration element to table failed_elements
	 */
	function add_failed_element($failed_id,$table)
	{
		$query = 'insert into failed_elements(failed_id, table_name) values ('.
					$failed_id . ','.$table.')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * add a migrated file to the table recovery to make a rollback action possible
	 */
	function add_recovery_element($old_path,$new_path)
	{
		$query = 'insert into recovery(old_path, new_path) values ('.
					$old_path . ','.$new_path .')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * add an id reference to the table id_reference
	 */
	function add_id_reference_element($old_id,$new_id,$table_name)
	{
		$query = 'insert into recovery(old_id, new_id, table_name) values ('.
					$old_id . ','.$new_id . ',' . $table_name . ')';
		$this->db_lcms->query($query);
	}
	
	/**
	 * 
	 */
}

?>
