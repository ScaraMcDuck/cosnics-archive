<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a user course category
 * @author Van Wayenbergh David
 */

abstract class ImportUserCourseCategory extends Import
{
	abstract function is_valid_user_course_category();
	abstract function convert_to_new_user_course_categorie();
	abstract static function get_all_users_courses_categories();
}
?>