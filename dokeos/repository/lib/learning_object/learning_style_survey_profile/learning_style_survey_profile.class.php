<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyProfile extends LearningObject
{
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_PROFILE_METADATA = 'profile_metadata';
	
	private $metadata;

	function get_survey_id ()
	{
		return $this->get_additional_property(self :: PROPERTY_SURVEY_ID);
	}
	
	function get_profile_metadata ($name = null)
	{
		$this->load_profile_metadata();
		return (is_null($name)
			? $this->metadata
			: $this->metadata[$name]);
	}
	
	function set_survey_id ($sid)
	{
		return $this->set_additional_property(self :: PROPERTY_SURVEY_ID, $sid);
	}
	
	function set_profile_metadata ($name, $value)
	{
		if (is_null($name))
		{
			$this->metadata = $value;
		}
		else
		{
			$this->load_profile_metadata();
			$this->metadata[$name] = $value;			
		}
		return $this->set_additional_property(
			self :: PROPERTY_PROFILE_METADATA,
			serialize($this->metadata)
		);
	}
	
	private function load_profile_metadata()
	{
		if (!isset($this->metadata))
		{
			$serialized = $this->get_additional_property(self :: PROPERTY_PROFILE_METADATA);
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