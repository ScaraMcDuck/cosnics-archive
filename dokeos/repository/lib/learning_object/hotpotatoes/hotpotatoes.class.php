<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an open question
 */
class Hotpotatoes extends LearningObject
{
	const PROPERTY_PATH = 'path';
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_PATH,);
	}
	
	function get_path()
	{
		return $this->get_additional_property(self :: PROPERTY_PATH);
	}
	
	function set_path($path)
	{
		return $this->set_additional_property(self :: PROPERTY_PATH, $path);
	}
	
	function delete()
	{
		$this->delete_file();
		parent :: delete();
	}
	
	function delete_file()
	{
		$path = Path :: get(SYS_REPO_PATH) . $this->get_path();
		Filesystem::remove($path);
	}
}
?>