<?php

/**
 * @package migration
 */

abstract class Import_Course extends Import
{
	abstract function convertToNewCourse();
	abstract static function GetAllCourses();
}
?>
