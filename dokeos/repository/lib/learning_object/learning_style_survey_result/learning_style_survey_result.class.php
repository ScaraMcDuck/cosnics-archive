<?php

require_once dirname(__FILE__) . '/../../learning_object.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyResult extends LearningObject
{
	// Note: Owner = test subject, hence no property for that
	const PROPERTY_PROFILE_ID = 'profile_id';
	const PROPERTY_RESULT_METADATA = 'result_metadata';
	
	private $metadata;
	
	private $answers;
	
	private $profile;
	
	function get_result_answers ()
	{
		if (!$this->answers)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->answers = $dm->retrieve_learning_objects(
				'learning_style_survey_user_answer',
				new EqualityCondition(LearningObject :: PROPERTY_PARENT_ID, $this->get_id())
			)->as_array();
		}
		return $this->answers;
	}

	function get_profile()
	{
		if (!$this->profile)
		{
			$dm = RepositoryDataManager :: get_instance();
			$this->profile = $dm->retrieve_learning_object(
				$this->get_profile_id(),
				'learning_style_survey_profile'
			);
		}
		return $this->profile;
	}

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
	
	function is_master_type()
	{
		return false;
	}
	
	static function get_additional_property_names()
	{
		return array (self :: PROPERTY_PROFILE_ID, self :: PROPERTY_RESULT_METADATA);
	}
}

?>