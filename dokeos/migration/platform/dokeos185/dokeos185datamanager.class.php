<?php

/**
 * package migration.platform.dokeos185
 */
require_once(dirname(__FILE__) . '/../../lib/migrationdatamanager.class.php');
require_once(Path :: get_admin_path().'lib/admindatamanager.class.php');
require_once 'MDB2.php';

/**
 * Class that connects to the old dokeos185 system
 * 
 * @author Sven Vanpoucke
 * @author David Van Wayenbergh
 */
class Dokeos185DataManager extends MigrationDataManager
{	
	/**
	 * MDB2 instance 
	 */
	private $db;
	private $_configuration;
	private static $move_file;
	
	function Dokeos185DataManager($old_directory)
	{	
		parent :: MigrationDataManager();
		$this->get_configuration($old_directory);
	}
	
	function get_configuration($old_directory)
	{
		$old_directory = 'file://' . $old_directory;
		
		if(file_exists($old_directory) && is_dir($old_directory))
		{
			$config_file = $old_directory . '/main/inc/conf/configuration.php';
			if(file_exists($config_file) && is_file($config_file))
			{
				require_once($config_file);

				$this->_configuration = $_configuration;
			}
		}
	}
	
	/**
	 * Function to validate the dokeos 185 settings given in the wizard
	 * @param Array $parameters settings from the wizard
	 * @return true if settings are valid, otherwise false
	 */
	function validate_settings()
	{		
		if(mysql_connect($this->_configuration['db_host'], $this->_configuration['db_user'], 
						 $this->_configuration['db_password']	))
		{
			
			if(mysql_select_db($this->_configuration['main_database']) &&
			   mysql_select_db($this->_configuration['statistics_database']) &&
			    mysql_select_db($this->_configuration['user_personal_database']))
					return true;
		}	
		
		return false;
	}

	/**
	 * Connect to the dokeos185 database with login data from the $$this->_configuration
	 * @param String $dbname with databasename 
	 */
	function db_connect($dbname)
	{	
		$param = isset($this->_configuration[$dbname])?$this->_configuration[$dbname]:$dbname;
		$host = $this->_configuration['db_host'];
		$pos = strpos($host, ':');		

		if($pos ==! false)
		{
			$array = split(':', $host);
			$socket = $array[count($array) - 1];
			$host = 'unix(' . $socket . ')';
		}
		
		$dsn = 'mysql://'.$this->_configuration['db_user'].':'.$this->_configuration['db_password'].'@'.
				$host.'/'.$param;
		$this->db = MDB2 :: connect($dsn);
	}
	
	/**
	 * Get all the users from the dokeos185 database
	 * @return array of Dokeos185User
	 */
	function get_all_users()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM user';
		$result = $this->db->query($query);
		$users = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$users[] = $this->record_to_user($record);
			
		}
		$result->free();
		
		foreach($users as $user)
		{
			$query_admin = 'SELECT * FROM admin WHERE user_id=' . $user->get_user_id();
			$result_admin = $this->db->query($query_admin);
			
			if($result_admin->numRows() == 1)
			{
				$user->set_platformadmin(1);
			}
			
			$result_admin->free();
		}
		
		return $users;
	}
	
	/**
	 * Map a resultset record to a Dokeos185User Object
	 * @param ResultSetRecord $record from database
	 * @return Dokeos185User object with mapped data
	 */
	function record_to_user($record)
	{
		if (!is_array($record) || !count($record))
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}
		$defaultProp = array ();
		foreach (Dokeos185User :: get_default_user_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		return new Dokeos185User($defaultProp);
	}
	
	/**
	 * Move a file to a new place, makes use of FileSystem class
	 * Built in checks for same filename
	 * @param String $old_rel_path Relative path on the old system
	 * @param String $new_rel_path Relative path on the LCMS system
	 */
	function move_file($old_rel_path, $new_rel_path,$filename)
	{
		$old_path = $this->append_full_path(false, $old_rel_path);
		$new_path = $this->append_full_path(true, $new_rel_path);
		
		$old_file = $old_path . $filename;
		$new_file = $new_path . $filename;

		if(!file_exists($old_file) || !is_file($old_file)) return null;
		
		$new_filename = FileSystem :: copy_file_with_double_files_protection($old_path,
			$filename, $new_path, $filename, self:: $move_file);
		
		$this->add_recovery_element($old_file, $new_file);
			
		return($new_filename);
			
		// FileSystem :: remove($old_file);
	}
	
	/**
	 * Create a directory 
	 * @param boolean $is_new_system Which system the directory has to be created on (true = LCMS)
	 * @param String $rel_path Relative path on the chosen system
	 */
	function create_directory($is_new_system, $rel_path)
	{		
		FileSystem :: create_dir($this->append_full_path($is_new_system, $rel_path));
	}
	
	/**
	 * Function to return the full path
	 * @param boolean $is_new_system Which system the directory has to be created on (true = LCMS)
	 * @param String $rel_path Relative path on the chosen system
	 */
	function append_full_path($is_new_system, $rel_path)
	{
		if($is_new_system)
			$path = Path :: get_path(SYS_PATH).$rel_path;
		else
			$path = $this->_configuration['root_sys'].$rel_path;
		
		return $path;
	}
	
	/** Get all the categories of the courses from the dokeos185 database
	 * @return array of Dokeos185CourseCategory
	 */
	function get_all_course_categories()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM course_category';
		$result = $this->db->query($query);
		$course_categories = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_categories[] = $this->record_to_classobject($record, 'Dokeos185CourseCategory');
		}
		$result->free();
		
		return $course_categories;
	}
	
	/** Get all the courses from the dokeos185 database
	 * @return array of Dokeos185Courses
	 */
	function get_all_courses()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM course';
		$result = $this->db->query($query);
		$courses = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$courses[] = $this->record_to_classobject($record, 'Dokeos185Course');
			
		}
		$result->free();
		
		return $courses;
	}
	
	/** Get all the class relations of courses from the dokeos185 database
	 * @return array of Dokeos185CoursesRelClass
	 */
	function get_all_course_rel_classes()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM course_rel_class';
		$result = $this->db->query($query);
		$course_rel_classes = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_rel_classes[] = $this->record_to_classobject($record, 'Dokeos185CourseRelClass');
			
		}
		$result->free();
		
		return $course_rel_classes;
	}
	
	/** Get all the user relations of courses from the dokeos185 database
	 * @return array of Dokeos185CoursesRelUser
	 */
	function get_all_course_rel_user()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM course_rel_user';
		$result = $this->db->query($query);
		$course_rel_users = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_rel_users[] = $this->record_to_classobject($record, 'Dokeos185CourseRelUser');
			
		}
		$result->free();
		
		return $course_rel_users;
	}
	
	/** Get all the user courses category relations from the dokeos185 database
	 * @return array of Dokeos185UserCourseCategory
	 */
	function get_all_users_courses_categories()
	{
		$this->db_connect('user_personal_database');
		$query = 'SELECT * FROM user_course_category';
		$result = $this->db->query($query);
		$users_courses_categories = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$users_courses_categories[] = $this->record_to_classobject($record, 'Dokeos185UserCourseCategory');
			
		}
		$result->free();
		
		return $users_courses_categories;
	}
	
	/** Get all the tools from the dokeos185 database
	 * @return array of Dokeos185Annoucements
	 */
	function get_all_tools($db)
	{
		$this->db_connect($db);
		$query = 'SELECT * FROM tool';
		$result = $this->db->query($query);
		$tools = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$tools[] = $this->record_to_classobject($record, 'Dokeos185Tool');
			
		}
		$result->free();
		
		return $tools;
	}
	
	/**
	 * Get all the course descriptions from the dokeos185 database
	 */
	function get_all_course_descriptions($db)
	{
		$this->db_connect($db);
		$query = 'SELECT * FROM course_description';
		$result = $this->db->query($query);
		$course_descriptions = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$course_descriptions[] = $this->record_to_classobject($record, 'Dokeos185CourseDescription');
			
		}
		$result->free();
		
		return $course_descriptions;
	}
	
	/** Get all the Class from the dokeos185 database
	 * @return array of Dokeos185Class
	 */
	function get_all_classes()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM class';
		$result = $this->db->query($query);
		$classes = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$classes[] = $this->record_to_classobject($record, 'Dokeos185Class');
			
		}
		$result->free();
		
		return $classes;
	}
		
	/** Get all the Class_User from the dokeos185 database
	 * @return array of Dokeos185ClassUser
	 */
	function get_all_class_users()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM class_user';
		$result = $this->db->query($query);
		$class_users = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$class_users[] = $this->record_to_classobject($record, 'Dokeos185ClassUser');
			
		}
		$result->free();
		
		return $class_users;
	}
		
	/** Get all the current settings from the dokeos185 database
	 * @return array of Dokeos185SettingCurrent
	 */
	function get_all_current_settings()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM settings_current WHERE category = \'Platform\'';
		$result = $this->db->query($query);
		$settings_current = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$settings_current[] = $this->record_to_classobject($record, 'Dokeos185SettingCurrent');
			
		}
		$result->free();
		
		return $settings_current;
	}
		
	/** Get all the system announcements from the dokeos185 database
	 * @return array of Dokeos185SystenAnnoucements
	 */
	function get_all_system_announcements()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM sys_announcement';
		$result = $this->db->query($query);
		$settings_current = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$system_annoucements[] = $this->record_to_classobject($record, 'Dokeos185SystemAnnouncement');
			
		}
		$result->free();
		
		return $system_annoucements;
	}
	
	/** 
	 * Get all the personal agendas from the dokeos185 database
	 * @return array of Dokeos185PersonalAgenda
	 */
	function get_all_personal_agendas()
	{
		$this->db_connect('user_personal_database');
		$query = 'SELECT * FROM personal_agenda';
		$result = $this->db->query($query);
		$personal_agendas = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$personal_agendas[] = $this->record_to_classobject($record, 'Dokeos185PersonalAgenda');
			
		}
		$result->free();
		
		return $personal_agendas;
	}
	
	/**
	 * Get the first admin id
	 * @return admin_id
	 */
	function get_old_admin_id()
	{
		$this->db_connect('main_database');
		$query = 'SELECT * FROM `user` WHERE EXISTS
	(SELECT user_id FROM admin WHERE user.user_id = admin.user_id)';
		$result = $this->db->query($query);
		$personal_agendas = array();
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		$id = $record['user_id'];
		$result->free();
		
		return $id;
	}
	
	function get_item_property($db, $tool, $id)
	{
		$this->db_connect($db);
		
		$query = 'SELECT * FROM item_property WHERE tool = \'' . $tool . 
		'\' AND ref = ' . $id;
		
		$result = $this->db->query($query);
		$record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
		
		foreach (Dokeos185ItemProperty :: get_default_property_names() as $prop)
		{
			$defaultProp[$prop] = $record[$prop];
		}
		
		$result->free();
		
		return new Dokeos185ItemProperty($defaultProp);
	}
	
	/** Get all the announcements from the dokeos185 database
	 * @return array of Dokeos185Annoucements
	 */
	function get_all_announcements($db, $include_deleted_files)
	{
		$this->db_connect($db);
		$query = 'SELECT * FROM announcement';
		
		if($include_deleted_files != 1)
			$query = $query . ' WHERE id IN (SELECT ref FROM item_property WHERE tool=\'announcement\'' .
					' AND visibility <> 2);';

		$result = $this->db->query($query);
		$announcements = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$annoucements[] = $this->record_to_classobject($record, 'Dokeos185Announcement');
			
		}
		$result->free();
		
		return $annoucements;
	}
	
	/** Get all the calendar events from the dokeos185 database
	 * @return array of Dokeos185CalendarEvents
	 */
	function get_all_calendar_events($course, $include_deleted_files)
	{
		$this->db_connect($course->get_db_name());
		$query = 'SELECT * FROM calendar_event';
		
		if($include_deleted_files != 1)
			$query = $query . ' WHERE id IN (SELECT ref FROM item_property WHERE tool=\'calendar_event\'' .
					' AND visibility <> 2);';
		
		$result = $this->db->query($query);
		$calendar_events = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$calendar_events[] = $this->record_to_classobject($record, 'Dokeos185CalendarEvent');
			
		}
		$result->free();
		
		return $calendar_events;
	}
	
	/** Get all the links from the dokeos185 database
	 * @return array of Dokeos185Links
	 */
	function get_all_links($db, $include_deleted_files)
	{
		$this->db_connect($db);
		$query = 'SELECT * FROM link';
		
		if($include_deleted_files != 1)
			$query = $query . ' WHERE id IN (SELECT ref FROM item_property WHERE tool=\'link\'' .
					' AND visibility <> 2);';

		$result = $this->db->query($query);
		$links = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$links[] = $this->record_to_classobject($record, 'Dokeos185Link');
			
		}
		$result->free();
		
		return $links;
	}
	
	/** Get all the link categories from the dokeos185 database
	 * @return array of Dokeos185LinkCategory
	 */
	function get_all_link_categories($db)
	{	
		$this->db_connect($db);
		$query = 'SELECT * FROM link_category';
		
		$result = $this->db->query($query);
		$link_categories = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$link_categories[] = $this->record_to_classobject($record, 'Dokeos185LinkCategory');
		}
		$result->free();

		return $link_categories;
	}
	
	/** Get all the documents from the dokeos185 database
	 * @return array of Dokeos185Documents
	 */
	function get_all_documents($course, $include_deleted_files)
	{
		$this->db_connect($course->get_db_name());
		$query = 'SELECT * FROM document WHERE filetype <> \'folder\'';
		
		if($include_deleted_files != 1)
			$query = $query . ' AND id IN (SELECT ref FROM item_property WHERE tool=\'document\'' .
					' AND visibility <> 2);';
		
		$result = $this->db->query($query);
		$documents = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$documents[] = $this->record_to_classobject($record,'Dokeos185Document');
			
		}
		$result->free();
		
		return $documents;
	}
	
	function get_all_groups($course_db)
	{
		$this->db_connect($course_db);
		$query = 'SELECT * FROM group_info';
		
		$result = $this->db->query($query);
		$groups = array();
		while($record = $result->fetchRow(MDB2_FETCHMODE_ASSOC))
		{
			$groups[] = $this->record_to_classobject($record, 'Dokeos185Group');
		}
		$result->free();

		return $groups;
	}
	
	static function set_move_file($move_file)
	{
		self :: $move_file = $move_file;
	}
	
}

?>
