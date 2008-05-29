<?php
/**
 * $Id$
 * @package repository
 * 
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once dirname(__FILE__).'/repository_data_manager.class.php';
require_once dirname(__FILE__).'/difference_engine.class.php';
/**
 * A class to display a LearningObject.
 */
abstract class LearningObjectDifference
{
	/**
	 * The learning object.
	 */
	private $object;
	/**
	 * The learning object version.
	 */
	private $version;
	/**
	 * Constructor.
	 * @param LearningObject $object The learning object to compare.
	 * @param LearningObject $version The learning object to compare with.
	 */
	protected function LearningObjectDifference($object, $version)
	{
		$this->object = $object;
		$this->version = $version;
	}
	/**
	 * Returns the learning object associated with this object.
	 * @return LearningObject The object.
	 */
	function get_object()
	{
		return $this->object;
	}

	/**
	 * Returns the learning object associated with this object.
	 * @return LearningObject The object version.
	 */
	function get_version()
	{
		return $this->version;
	}

	function get_difference()
	{
		$object_string = $this->object->get_description();
        $object_string = str_replace('<p>', '', $object_string);
        $object_string = str_replace('</p>', "<br />\n", $object_string);
        $object_string = explode("\n", strip_tags($object_string));

        $version_string = $this->version->get_description();
        $version_string = str_replace('<p>', '', $version_string);
        $version_string = str_replace('</p>', "<br />\n", $version_string);
		$version_string = explode("\n", strip_tags($version_string));

		$td = new Difference_Engine($version_string, $object_string);

		return $td->getDiff();
	}

	/**
	 * Creates an object that can display the given learning object in a
	 * standardized fashion.
	 * @param LearningObject $object The object to display.
	 * @return LearningObject
	 */
	static function factory(&$object, &$version)
	{
		$type = $object->get_type();
		$class = LearningObject :: type_to_class($type).'Difference';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_difference.class.php';
		return new $class($object, $version);
	}
}
?>