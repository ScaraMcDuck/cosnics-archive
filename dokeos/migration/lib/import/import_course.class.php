<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Course extends Import
{
	abstract function convertToNewCourse();
	abstract static function GetAllCourses();
}
?>
