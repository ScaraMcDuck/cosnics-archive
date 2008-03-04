<?php

/**
 * @package migration.lib.import
 */

abstract class ImportCourseCategory extends Import
{
	abstract function convert_to_new_course_category();
	abstract static function get_all_course_categories();
}
?>