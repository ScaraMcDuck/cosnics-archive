<?php
/**
 * @package migration.lib.import
 */

abstract class ImportCourseRelClass extends Import
{
	abstract function convert_to_new_course_rel_class();
	abstract static function get_all_courses_rel_classes();
}
?>
