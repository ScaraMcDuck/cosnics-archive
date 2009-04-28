<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage learning_path
 */
class ScormItem extends LearningObject
{
	const PROPERTY_PATH = 'path';
	const PROPERTY_VISIBLE = 'visible';
	const PROPERTY_PARAMETERS = 'parameters';
	const PROPERTY_TIME_LIMIT_ACTION = 'time_limit_action';
	const PROPERTY_DATA_FROM_LMS = 'data_from_lms';
	const PROPERTY_COMPLETION_TRESHOLD = 'completion_treshold';
	const PROPERTY_HIDE_LMS_UI = 'hide_lms_ui';
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_PATH, self :: PROPERTY_VISIBLE, self :: PROPERTY_PARAMETERS,
					  self :: PROPERTY_TIME_LIMIT_ACTION, self :: PROPERTY_DATA_FROM_LMS, self :: PROPERTY_COMPLETION_TRESHOLD,
					  self :: PROPERTY_HIDE_LMS_UI );
	}
	
	function get_path()
	{
		return $this->get_additional_property(self :: PROPERTY_PATH);
	}
	
	function set_path($path)
	{
		$this->set_additional_property(self :: PROPERTY_PATH, $path);
	}
	
	function get_visible()
	{
		return $this->get_additional_property(self :: PROPERTY_VISIBLE);
	}
	
	function set_visible($visible)
	{
		$this->set_additional_property(self :: PROPERTY_VISIBLE, $visible);
	}
	
	function get_parameters()
	{
		return $this->get_additional_property(self :: PROPERTY_PARAMETERS);
	}
	
	function set_parameters($parameters)
	{
		$this->set_additional_property(self :: PROPERTY_PARAMETERS, $parameters);
	}
	
	function get_time_limit_action()
	{
		return $this->get_additional_property(self :: PROPERTY_TIME_LIMIT_ACTION);
	}
	
	function set_time_limit_action($time_limit_action)
	{
		$this->set_additional_property(self :: PROPERTY_TIME_LIMIT_ACTION, $time_limit_action);
	}
	
	function get_data_from_lms()
	{
		return $this->get_additional_property(self :: PROPERTY_DATA_FROM_LMS);
	}
	
	function set_data_from_lms($data_from_lms)
	{
		$this->set_additional_property(self :: PROPERTY_DATA_FROM_LMS, $data_from_lms);
	}
	
	function get_completion_treshold()
	{
		return $this->get_additional_property(self :: PROPERTY_COMPLETION_TRESHOLD);
	}
	
	function set_completion_treshold($completion_treshold)
	{
		$this->set_additional_property(self :: PROPERTY_COMPLETION_TRESHOLD, $completion_treshold);
	}
	
	function get_hide_lms_ui()
	{
		return $this->get_additional_property(unserialize(self :: PROPERTY_HIDE_LMS_UI));
	}
	
	function set_hide_lms_ui($hide_lms_ui)
	{
		$this->set_additional_property(self :: PROPERTY_HIDE_LMS_UI, serialize($hide_lms_ui));
	}
	
	function get_url()
	{
		return Path :: get(WEB_REPO_PATH) . $this->get_path();
	}
	
	function get_full_path()
	{
		return Path :: get(SYS_REPO_PATH) . $this->get_path();
	}
	
	function delete()
	{
		$path = Path :: get(SYS_REPO_PATH) . $this->get_path();
		Filesystem::remove($path);
		parent :: delete();
	}
}
?>