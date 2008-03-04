<?php

/**
 * @package migration.lib.import
 */

abstract class ImportUserCourseCategory extends Import
{
	abstract function convert_to_new_user_course_categorie();
	abstract static function get_all_users_courses_categories();
}
?>