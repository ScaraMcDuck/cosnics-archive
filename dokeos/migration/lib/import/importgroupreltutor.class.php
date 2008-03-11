<?php

/**
 * @package migration.lib.import
 */

/**
 * Abstract class that defines a group tutor relation
 * @author Sven Vanpoucke
 */
abstract class ImportGroupRelTutor extends Import
{
	abstract function is_valid_group_rel_tutor($course);
	abstract function convert_to_new_group_rel_tutor($course);
	abstract static function get_all_groups_rel_tutor($course, $mgdm);
}
?>