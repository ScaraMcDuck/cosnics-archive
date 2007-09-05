<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyResult extends LearningObject
{
	// Note: Owner = test subject, hence no property for that
	const PROPERTY_PROFILE_ID = 'profile_id';
	const PROPERTY_RESULT_METADATA = 'result_metadata';
	
	private $metadata;

	function get_profile_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_PROFILE_ID);
	}
	
	function get_result_metadata ($name = null)
	{
		$this->load_result_metadata();
		return (is_null($name)
			? $this->metadata
			: $this->metadata[$name]);
	}
	
	function set_profile_id ($pid)
	{
		return $this->set_additional_property(self :: PROPERTY_PROFILE_ID, $pid);
	}
	
	function set_result_metadata ($name, $value)
	{
		if (is_null($name))
		{
			$this->metadata = $value;
		}
		else
		{
			$this->load_result_metadata();
			$this->metadata[$name] = $value;			
		}
		return $this->set_additional_property(
			self :: PROPERTY_RESULT_METADATA,
			serialize($this->metadata)
		);
	}
	
	private function load_result_metadata()
	{
		if (!isset($this->metadata))
		{
			$serialized = $this->get_additional_property(self :: PROPERTY_RESULT_METADATA);
			$this->metadata = (isset($serialized)
				? unserialize($serialized)
				: array());
		}
	}
	
	function is_versionable()
	{
		return false;
	}
}

?>