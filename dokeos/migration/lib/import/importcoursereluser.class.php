<?php

/**
 * @package migration.lib.import
 */

abstract class ImportCourseRelUser extends Import
{
	abstract function convert_to_new_course_rel_user();
	abstract static function get_all_course_rel_users();
}
?>