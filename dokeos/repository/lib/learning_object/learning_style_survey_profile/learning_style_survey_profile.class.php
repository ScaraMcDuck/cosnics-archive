<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyProfile extends LearningObject
{
	const PROPERTY_SURVEY_ID = 'survey_id';
	const PROPERTY_PROFILE_METADATA = 'profile_metadata';
	
	private $metadata;
	
	private $survey;
	
	function get_survey()
	{
		if (!$this->survey)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->survey = $dm->retrieve_learning_object(
				$this->get_survey_id(),
				'learning_style_survey'
			);
		}
		return $this->survey;
	}

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
	
	function set_survey ($survey)
	{
		return $this->set_survey_id($survey->get_id());
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
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_SURVEY_ID, self :: PROPERTY_PROFILE_METADATA);
	}
}

?>