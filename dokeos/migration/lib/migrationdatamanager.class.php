<?php
/**
 * package migration.platform.dokeos185
 * 
 * @author Van Wayenbergh David
 * @author Vanpoucke Sven
 */
require_once(Path :: get_library_path().'configuration/configuration.php');
require_once dirname(__FILE__) . '/../../repository/lib/repositorydatamanager.class.php';
require_once Path :: get_library_path().'installer.class.php';

/**
 * Abstract class from which the system datamanagers can extend
 * also used for communication with LCMS databases
 */
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
	const TEMP_FILES_M5_TABLE = 'temp_files_md5';
	
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
	
	/**
	 * Constructor, connect to lcms database
	 */
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
	 * Debug method
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
	
	/**
	 * Create a storage unit in the database
	 * @param string $name name of the table
	 * @param array $properties properties of the table
	 * @param array $indexes indexes of the table
	 */
	function create_storage_unit($name,$properties,$indexes)
	{
		$this->db_lcms->loadModule('Manager');
		$manager = $this->db_lcms->manager;
		$tables = $manager->listTables();
		if( in_array($name,$tables))
		{
			$manager->dropTable($name);
		}
		
		$name = $this->get_table_name($name);
		
		$options['charset'] = 'utf8';
		$options['collate'] = 'utf8_unicode_ci';
		if (!MDB2 :: isError($manager->createTable($name,$properties,$options)))
		{
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

	/**
	 * Expands a table identifier to the real table name. Currently, this
	 * method prefixes the given table name.
	 * @param string $name The table identifier.
	 * @return string The actual table name.
	 */
	function get_table_name($name)
	{
		$dsn = $this->db_lcms->getDSN('array');
		return $dsn['database'].'.'.$name;
	}
	
	/**
	 * gets the parent_id from a learning object
	 * 
	 * @param int $owner id of the owner of the learning object
	 * @param String $type type of the learning object
	 * @param String $title title of the learning object
	 * @return $record returns a parent_id
	 */
	function get_parent_id($owner,$type,$title,$parent = null)
	{
		$this->db_lcms_connect();
		$query = 'SELECT id FROM repository_learning_object WHERE owner=\'' . $owner . '\' AND type=\'' . $type .
		 		'\' AND title=\'' . $title . '\'';
		
		if($parent)
			$query = $query . ' AND parent=' . $parent;
	
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
		$dir = dirname(__FILE__) . '/data_manager/';
		$files = FileSystem :: get_directory_content($dir, FileSystem :: LIST_FILES);
		
		foreach($files as $file)
		{
			if ((substr($file, -3) == 'xml'))
			{
				$storage_unit_info = Installer :: parse_xml_file($file);
				$this->create_storage_unit($storage_unit_info['name'],$storage_unit_info['properties'],$storage_unit_info['indexes']);
			}
		}
		
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
	
	/**
	 * add a migrated file to the table recovery to make a rollback action possible
	 * @param String $old_path the old path of an element
	 * @param String $new_path the new path of an element
	 */
	function add_recovery_element($old_path,$new_path)
	{
		$this->db_lcms_connect();
		
		$pos = strpos($old_path,'\'');
		if (pos != -1)
			$old_path = substr($old_path,0,$pos-1) . '\\' . substr($old_path,$pos);
		
		$pos = strpos($new_path,'\'');
		if (pos != -1)
			$new_path = substr($new_path,0,$pos-1) . '\\' . substr($new_path,$pos);
			
		$query = 'INSERT INTO ' . self :: TEMP_RECOVERY_TABLE .
				 '(old_path, new_path) VALUES (\''.
					$old_path . '\',\''.$new_path .'\')';
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
	
	/**
	 * Adds an md5 of a file to the database for later checks
	 */
	function add_file_md5($user_id, $document_id, $md5)
	{
		$this->db_lcms_connect();
		$query = 'INSERT INTO ' . self :: TEMP_FILES_M5_TABLE . 
				 ' (user_id, document_id, file_md5) VALUES (\'' .
					$user_id . '\',\'' . $document_id . '\',\'' . $md5 . '\')';
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
	 
	 /**
	  * Generic method to create a classobject from a record
	  */
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
	  * Selects a document id from the files_md5 table
	  */
	 function get_document_from_md5($user_id, $md5)
	 {
	 	$this->db_lcms_connect();
	 	$query = 'SELECT document_id FROM ' . self :: TEMP_FILES_M5_TABLE . 
				 ' WHERE user_id = \'' . $user_id . '\' AND file_md5=\'' . $md5 . '\'';
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		if($record)
			return $record['document_id'];
			
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
	 	$query = 'SELECT * FROM ' . $table_name . ' WHERE ';
	 	if ($table_name == 'weblcms_course')
	 		$query = $query . 'code=\'' . $code . '\'';
	 	else
	 		$query = $query . 'id=\'' . $code . '\'';
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
	
	/**
	 * Gets the parent id of weblcmslearningobjectpublicationcategory
	 * 
	 */
	function publication_category_exist($title,$course_code,$tool,$parent = null)
	{
		$this->db_lcms_connect();
		
		$query = 'SELECT id FROM weblcms_learning_object_publication_category WHERE title=\'' . $title . '\' AND course=\'' . $course_code .
		 		'\' AND tool=\'' . $tool . '\'';
		
		if($parent)
			$query = $query . ' AND parent=' . $parent;
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		return $record['id'];
	}
	
	/**
	 * Retrieve the document id with give owner and document path
	 * @param string $path path of the document
	 * @param int $owner 
	 */
	function get_document_id($path,$owner_id)
	{
		$this->db_lcms_connect();
		
		$query = 'SELECT id FROM repository_document WHERE path=\'' . $path . '\' AND id IN ' .
						'(SELECT id FROM repository_learning_object WHERE owner = ' . $owner_id . ')';
		
		$result = $this->db_lcms->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$result->free();
		
		return $record['id'];
	}

	/*function get_owner($course)
	{
		$this->db_lcms_connect();
		
		$query = 'SELECT user_id FROM weblcms_course_rel_user WHERE course_code = \'' . $course . '\'';
		
		$result = $this->db_lcms->query($query);
		$owners = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$owners[] = $record['user_id'];
			
		}
		$result->free();
		
		if (count($owners) == 1)
		{
			return $owners[0];
		}
		else
		{
			$query = 'SELECT CRL.user_id FROM weblcms_course_rel_user CRL 
					JOIN user_user UU ON UU.user_id = CRL.user_id
					JOIN weblcms_course C ON C.code = CRL.course_code
					WHERE CRL.status = 1 AND CRL.course_code = \'' . $course . 
					'\' AND C.tutor_name = (UU.firstname + \' \' + UU.lastname)';
			
			$result = $this->db_lcms->query($query);
			$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			$result->free();
			if ($record)
				return $record['user_id'];
			else
			{
				$query = 'SELECT COUNT(LOP.publisher) as count, CRL.user_id FROM weblcms_course_rel_user CRL
						JOIN weblcms_learning_object_publication LOP ON LOP.publisher = CRL.user_id
						AND LOP.course = CRL.course_code WHERE CRL.status = 1 AND
						CRL.course_code = \'' . $course . '\' GROUP BY CRL.user_id';
				
				$result = $this->db_lcms->query($query);
				$owner_id = -1;
				$max_published = -1;
				
				while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					if($record['count'] > $max_published) 
					{
						$max_published = $record['count'];
						$owner_id = $record['user_id'];
					}
					
				}
				$result->free();
				
				if ($owner_id == -1 )
				{
					$query = 'SELECT user_id FROM user_user WHERE admin = 1';
					$result = $this->db_lcms->query($query);
					$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
					$owner_id = $record['user_id'];
				}
				return $owner_id;
			}
		}
	}*/

	/**
	 * Method to retrieve the best owner for an orphan
	 * @param string $course course code
	 */
	function get_owner($course)
	{
		$this->db_lcms_connect();
		
		$query = 'SELECT user_id FROM weblcms_course_rel_user WHERE course_code = \'' . $course . '\' AND status=1;';
		
		$result = $this->db_lcms->query($query);
		$owners = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$owners[] = $record['user_id'];
			
		}
		$result->free();
		
		if (count($owners) == 1)
		{
			return $owners[0];
		}
		else
		{
			$query = 'SELECT CRL.user_id FROM weblcms_course_rel_user CRL WHERE CRL.user_id IN (
					  SELECT UU.user_id FROM user_user UU WHERE CONCAT(UU.lastname,\' \',UU.firstname) IN (
					  SELECT C.tutor_name FROM weblcms_course C WHERE C.code = CRL.course_code)) AND CRL.status = 1 AND CRL.course_code = \'' . $course . '\';';
			
			$result = $this->db_lcms->query($query);
			$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
			$result->free();
			if ($record)
				return $record['user_id'];
			else
			{
				$query = 'SELECT COUNT(LOP.publisher) as count, LOP.publisher FROM weblcms_learning_object_publication LOP WHERE LOP.publisher IN (
						  SELECT CRL.user_id FROM weblcms_course_rel_user CRL WHERE CRL.course_code = \''. $course .'\' AND CRL.status = 1) AND
						  LOP.course = \''. $course .'\' GROUP BY LOP.publisher;';
				
				$result = $this->db_lcms->query($query);
				$owner_id = -1;
				$max_published = -1;
				
				while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
				{
					if($record['count'] > $max_published) 
					{
						$max_published = $record['count'];
						$owner_id = $record['publisher'];
					}
					
				}
				$result->free();
				
				if ($owner_id == -1)
				{
					$query = 'SELECT user_id FROM user_user WHERE admin = 1';
					$result = $this->db_lcms->query($query);
					$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
					$owner_id = $record['user_id'];
				}
				return $owner_id;
			}
		}
	}
	
	/**
	 * Retrieves a learning object 
	 * @param int $lp_id learning object id
	 * @param string $tool tool of where the learning object belongs
	 */
	function get_owner_learning_object($lp_id, $tool)
	{
		$datamanager = RepositoryDataManager::get_instance();
		$result = $datamanager->retrieve_learning_object($lp_id, $tool);
		return $result;
	}
	
	/**
	 * Retrieves a user by full name
	 * @param string $fullname the fullname of the user
	 */
	function get_user_by_full_name($fullname)
	{
		$this->db_lcms_connect();
		
		$fullname = str_replace('\'', '\\\'', $fullname);
		
	 	$query = 'SELECT user_id FROM user_user WHERE ' .
	 			 'CONCAT(firstname, \' \', lastname) = \'' . $fullname . '\' OR ' .
	 			 'CONCAT(lastname, \' \', firstname) = \'' . $fullname . '\'';

	 	$result = $this->db_lcms->query($query);
	 	$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
	 	if($record)
	 		return $record['user_id'];
	}
	
}

?>
