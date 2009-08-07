<?php
/**
 * @package repository.learningobject
 * @subpackage physical_location
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
/**
 * This class can be used to get the difference between physical_locations
 */
class PhysicalLocationDifference extends LearningObjectDifference
{
	function get_difference()
	{
		$object = $this->get_object();
		$version = $this->get_version();
		
		$object_string = $object->get_location();
        $object_string = explode("\n", strip_tags($object_string));
           	
        $version_string = $version->get_location();
		$version_string = explode("\n", strip_tags($version_string));
		
		$td = new Difference_Engine($version_string, $object_string);
		
		return array_merge(parent :: get_difference(), $td->getDiff());
	}
}
?>