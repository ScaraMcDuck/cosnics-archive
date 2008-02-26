<?php

/**
 * @package migration.lib.import
 */

abstract class Import_Unknown_Data
{
	abstract function convertToLearningObjects();
	abstract static function getAllUnknownData();
}

?>
