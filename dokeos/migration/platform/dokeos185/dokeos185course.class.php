<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importcourse.class.php';

/**
 * This class represents an old Dokeos 1.8.5 course
 *
 * @author David Van Wayenbergh
 */

class Dokeos185Course extends Import
{
	
	/**
	 * course properties
	 */
	const PROPERTY_CODE = 'code';
	const PROPERTY_DIRECTORY = 'directory';
	const PROPERTY_DB_NAME = 'db_name';
	const PROPERTY_COURSE_LANGUAGE = 'course_language';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_CATEGORY_CODE = 'category_code';
	const PROPERTY_VISIBILITY = 'visibility';
	const PROPERTY_SHOW_SCORE = 'show_score';
	const PROPERTY_TUTOR_NAME = 'tutor_name';
	const PROPERTY_VISUAL_CODE = 'visual_code';
	const PROPERTY_DEPARTMENT_NAME = 'department_name';
	const PROPERTY_DEPARTMENT_URL = 'department_url';
	const PROPERTY_DISK_QUOTA = 'disk_quota';
	const PROPERTY_LAST_VISIT = 'last_visit';
	const PROPERTY_LAST_EDIT = 'last_edit';
	const PROPERTY_CREATION_DATE = 'creation_date';
	const PROPERTY_EXPIRATION_DATE = 'expiration_date';
	const PROPERTY_TARGET_COURSE_CODE = 'target_course_code';
	const PROPERTY_SUBSCRIBE = 'subscribe';
	const PROPERTY_UNSUBSCRIBE = 'unsubscribe';
	const PROPERTY_REGISTRATION_CODE = 'registration_code';
	
	/**
	 * Alfanumeric identifier of the course object.
	 */
	private $code;
	
	/**
	 * Default properties of the course object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new course object.
	 * @param array $defaultProperties The default properties of the user
	 *                                 object. Associative array.
	 */
	function Dokeos185Course($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this course object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this course.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Get the default properties of all courses.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_CODE,self::PROPERTY_DIRECTORY,self::PROPERTY_DB_NAME,
		self::PROPERTY_COURSE_LANGUAGE,self::PROPERTY_TITLE,self::PROPERTY_DESCRIPTION,
		self::PROPERTY_CATEGORY_CODE,self::PROPERTY_VISIBILITY,self::PROPERTY_SHOW_SCORE,
		self::PROPERTY_TUTOR_NAME,self::PROPERTY_VISUAL_CODE,self::PROPERTY_DEPARTMENT_URL,
		self::PROPERTY_DISK_QUOTA,self::PROPERTY_LAST_VISIT,self::PROPERTY_LAST_EDIT,
		self::PROPERTY_CREATION_DATE,self::PROPERTY_EXPIRATION_DATE,self::PROPERTY_TARGET_COURSE_CODE,
		self::PROPERTY_SUBSCRIBE,self::PROPERTY_UNSUBSCRIBE,self::PROPERTY_REGISTRATION_CODE,
		self::PROPERTY_DEPARTMENT_NAME);
	}
	
	/**
	 * Sets a default property of this course by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default course
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	
	/**
	 * COURSE GETTERS AND SETTERS
	 */
	
	
	/**
	 * Returns the code of this course.
	 * @return String The code.
	 */
	function get_code()
	{
		return $this->code;
	}
	
	/**
	 * Returns the directory of this course.
	 * @return String The directory.
	 */
	function get_Directory()
	{
		return $this->directory;
	}
	
	/**
	 * Returns the db_name of this course.
	 * @return String The db_name.
	 */
	function get_db_name()
	{
		return $this->db_name;
	}
	
	/**
	 * Returns the course_language of this course.
	 * @return String The course_language.
	 */
	function get_course_language()
	{
		return $this->course_language;
	}
	
	/**
	 * Returns the title of this course.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->title;
	}
	
	/**
	 * Returns the description of this course.
	 * @return String The discription.
	 */
	function get_discription()
	{
		return $this->description;
	}
	
	/**
	 * Returns the category_code of this course.
	 * @return String The category_code.
	 */
	function get_category_code()
	{
		return $this->category_code;
	}
	
	/**
	 * Returns the visibility of this course.
	 * @return int The visibility.
	 */
	function get_visibility()
	{
		return $this->visibility;
	}
	
	/**
	 * Returns the show_score of this course.
	 * @return int The show_score.
	 */
	function get_show_score()
	{
		return $this->show_score;
	}
	
	/**
	 * Returns the tutor_name of this course.
	 * @return String The tutor_name.
	 */
	function get_tutor_name()
	{
		return $this->tutor_name;
	}
	
	/**
	 * Returns the visual_code of this course.
	 * @return String The visual_code.
	 */
	function get_visual_code()
	{
		return $this->visual_code;
	}
	
	/**
	 * Returns the department_name of this course.
	 * @return String The department_name.
	 */
	function get_department_name()
	{
		return $this->department_name;
	}
	
	/**
	 * Returns the department_url of this course.
	 * @return String The department_url.
	 */
	function get_department_url()
	{
		return $this->department_url;
	}
	
	/**
	 * Returns the disk_quota of this course.
	 * @return int The disk_quota.
	 */
	function get_disk_quota()
	{
		return $this->disk_quota;
	}
	
	/**
	 * Returns the last_visit of this course.
	 * @return String The last_visit.
	 */
	function get_last_visit()
	{
		return $this->last_visit;
	}
	
	/**
	 * Returns the last_edit of this course.
	 * @return String The last_edit.
	 */
	function get_last_edit()
	{
		return $this->last_edit;
	}
	
	/**
	 * Returns the creation_date of this course.
	 * @return String The creation_date.
	 */
	function get_creation_date()
	{
		return $this->creation_date;
	}
	
	/**
	 * Returns the expiration_date of this course.
	 * @return String The expiration_date.
	 */
	function get_expiration_date()
	{
		return $this->expiration_date;
	}
	
	/**
	 * Returns the target_course_code of this course.
	 * @return String The target_course_code.
	 */
	function get_target_course_code()
	{
		return $this->target_course_code;
	}
	
	/**
	 * Returns the subscribe of this course.
	 * @return int The subscribe.
	 */
	function get_subscribe()
	{
		return $this->subscribe;
	}
	
	/**
	 * Returns the unsubscribe of this course.
	 * @return int The unsubscribe.
	 */
	function get_unsubscribe()
	{
		return $this->unsubscribe;
	}
	
	/**
	 * Returns the registration_code of this course.
	 * @return String The registration_code.
	 */
	function get_registration_code()
	{
		return $this->registration_code;
	}
	
	/**
	 * Sets the code of this course.
	 * @param String $code The code.
	 */
	function set_code($code)
	{
		$this->code = $code;
	}
	
	/**
	 * Sets the directory of this course.
	 * @param String $directory The directory.
	 */
	function set_directory($directory)
	{
		$this->directory = $directory;
	}
	
	/**
	 * Sets the db_name of this course.
	 * @param String $db_name The db_name.
	 */
	function set_db_name($db_name)
	{
		$this->db_name = $db_name;
	}
	
	/**
	 * Sets the course_language of this course.
	 * @param String $course_language The course_language.
	 */
	function set_course_language($course_language)
	{
		$this->course_language = $course_language;
	}
	
	/**
	 * Sets the title of this course.
	 * @param String $title The title.
	 */
	function set_title($title)
	{
		$this->title = $title;
	}
	
	/**
	 * Sets the description of this course.
	 * @param String $description The description.
	 */
	function set_description($description)
	{
		$this->description = $description;
	}
	
	/**
	 * Sets the category_code of this course.
	 * @param String $category_code The category_code.
	 */
	function set_category_code($category_code)
	{
		$this->category_code = $category_code;
	}
	
	/**
	 * Sets the visibility of this course.
	 * @param int $visibility The visibility.
	 */
	function set_visibility($visibility)
	{
		$this->visibility = $visibility;
	}
	
	/**
	 * Sets the show_score of this course.
	 * @param String $show_score The show_score.
	 */
	function set_show_score($show_score)
	{
		$this->show_score = $show_score;
	}
	
	/**
	 * Sets the tutor_name of this course.
	 * @param String $tutor_name The tutor_name.
	 */
	function set_tutor_name($tutor_name)
	{
		$this->tutor_name = $tutor_name;
	}
	
	/**
	 * Sets the visual_code of this course.
	 * @param String $visual_code The visual_code.
	 */
	function set_visual_code($visual_code)
	{
		$this->visual_code = $visual_code;
	}
	
	/**
	 * Sets the visual_code of this course.
	 * @param String $visual_code The visual_code.
	 */
	function set_visual_code($visual_code)
	{
		$this->visual_code = $visual_code;
	}
	
	/**
	 * Sets the department_name of this course.
	 * @param String $department_name The department_name.
	 */
	function set_department_name($department_name)
	{
		$this->department_name = $department_name;
	}
	
	/**
	 * Sets the department_url of this course.
	 * @param String $department_url The department_url.
	 */
	function set_department_url($department_url)
	{
		$this->department_url = $department_url;
	}
	
	/**
	 * Sets the disk_quota of this course.
	 * @param Int $disk_quota The disk_quota.
	 */
	function set_disk_quota($disk_quota)
	{
		$this->disk_quota = $disk_quota;
	}
	
	/**
	 * Sets the last_visit of this course.
	 * @param String $last_visit The last_visit.
	 */
	function set_last_visit($last_visit)
	{
		$this->last_visit = $last_visit;
	}
	
	/**
	 * Sets the last_edit of this course.
	 * @param String $last_edit The last_edit.
	 */
	function set_last_edit($last_edit)
	{
		$this->last_edit = $last_edit;
	}
	
	/**
	 * Sets the creation_date of this course.
	 * @param String $creation_date The creation_date.
	 */
	function set_creation_date($creation_date)
	{
		$this->creation_date = $creation_date;
	}
	
	/**
	 * Sets the expiration_date of this course.
	 * @param String $expiration_date The expiration_date.
	 */
	function set_expiration_date($expiration_date)
	{
		$this->expiration_date = $expiration_date;
	}
	
	/**
	 * Sets the target_course_code of this course.
	 * @param String $target_course_code The target_course_code.
	 */
	function set_target_course_code($target_course_code)
	{
		$this->target_course_code = $target_course_code;
	}
	
	/**
	 * Sets the subscribe of this course.
	 * @param int $subscribe The subscribe.
	 */
	function set_subscribe($subscribe)
	{
		$this->subscribe = $subscribe;
	}
	
	/**
	 * Sets the unsubscribe of this course.
	 * @param int $unsubscribe The unsubscribe.
	 */
	function set_subscribe($unsubscribe)
	{
		$this->subscribe = $unsubscribe;
	}
	
	/**
	 * Sets the registration_code of this course.
	 * @param String $registration_code The registration_code.
	 */
	function set_registration_code($registration_code)
	{
		$this->registration_code = $registration_code;
	}
	
	/**
	 * Migration courses
	 */
	function convert_to_new_courses()
	{
		$mgdm = MigrationDataManager :: getInstance('Dokeos185');
		
		//Course parameters
		$lcms_course = new Course();
		$lcms_course->set_db($this->get_db_name());
		$lcms_course->set_path($this->get_directory());
		$lcms_course->set_language($this->get_course_language());
		$lcms_course->set_name($this->get_title());
		$lcms_course->set_category_code($this->get_category_code());
		$lcms_course->set_visibility($this->get_visibility());
		$lcms_course->set_titular($this->get_tutor_name());
		$lcms_course->set_visual($this->get_visual_code());
		$lcms_course->set_extlink_name($this->get_department_name());
		$lcms_course->set_extlink_url($this->get_department_url());
		$lcms_course->set_subscribe_allowed($this->get_subscribe());
		$lcms_course->set_unsubscribe_allowed($this->get_unsubscribe());
		
		//create course in database
		$lcms_course->create();
		
		//Add id references to temp table
		$mgdm->add_id_reference($this->get_code(), $lcms_course->get_id(), 'weblcms_course');
	}
}
?>
