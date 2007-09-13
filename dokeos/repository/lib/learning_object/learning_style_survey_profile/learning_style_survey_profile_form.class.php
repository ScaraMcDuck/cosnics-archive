<?php

require_once dirname(__FILE__) . '/learning_style_survey_profile.class.php';
require_once dirname(__FILE__) . '/../../learningobjectform.class.php';
require_once dirname(__FILE__) . '/../../repositorydatamanager.class.php';
require_once dirname(__FILE__) . '/../../../../common/condition/equalitycondition.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurveyProfileForm extends LearningObjectForm
{
	const PARAM_SURVEY_ID = 'lssp_survey_id';
	const PARAM_PROFILE_METADATA = 'lssp_profile_metadata';
	const PARAM_COMPLETE = 'lssp_complete';
	
	private $survey_element;
	
	private $metadata_elements;
	
	private $survey;
	
	function build_creation_form()
	{
		parent :: build_creation_form();
		$dm = RepositoryDataManager :: get_instance();
		$cond = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, api_get_user_id());
		$survey_map = array();
		$surveys = array();
		foreach ($dm->retrieve_learning_objects('learning_style_survey', $cond)->as_array() as $survey) {
			$survey_map[$survey->get_id()] = $survey;
			$surveys[$survey->get_id()] = $survey->get_title();
		}
		$this->survey_element = $this->add_select(self :: PARAM_SURVEY_ID, get_lang('Survey'), $surveys);
		$this->metadata_elements = array();
		if (parent :: validate())
		{
			$this->survey_element->freeze();
			$this->survey = $survey_map[$this->survey_element->exportValue()];
			$metadata_fields = $this->survey->get_additional_survey_parameters();
			// Extra check, because metadata elements are not required and will consequently always validate
			$this->addElement('hidden', self :: PARAM_COMPLETE, 1);
			if (count($metadata_fields))
			{
				foreach ($metadata_fields as $field => $help)
				{
					// TODO: use a JS tooltip or something
					$this->addElement('html', '<div class="row"><div class="label">' . $field . '</div>'
						. '<div class="formw">' . nl2br(htmlspecialchars($help)) . '</div></div>');
					$this->metadata_elements[$field] = $this->addElement('textarea', self :: PARAM_PROFILE_METADATA . '__' . $field, '&nbsp;', array('style' => 'width: 100%', 'rows' => 5));
				}
			}
			else
			{
				// No metadata, so we can validate immediately. This is
				// practically the same as publishing the survey itself, but
				// doing so would make things more complicated.
				$_POST[self :: PARAM_COMPLETE] = 1;
			}
		}
	}
	
	// Overridden to check for extra parameter--see above
	function validate()
	{
		return (parent :: validate() && $_POST[self :: PARAM_COMPLETE]);
	}
	
	function build_editing_form()
	{
		parent :: build_editing_form();
		// TODO
	}
	
	// Inherited
	function create_learning_object()
	{
		$object = new LearningStyleSurveyProfile();
		$object->set_survey_id($this->survey->get_id());
		$metadata = array();
		foreach ($this->metadata_elements as $param => $el)
		{
			$value = $el->exportValue();
			if (!empty($value))
			{
				$metadata[$param] = $value;
			}
		}
		$object->set_profile_metadata(null, $metadata);
		$this->set_learning_object($object);
		return parent :: create_learning_object();
	}
	
	function update_learning_object()
	{
		$object = $this->get_learning_object();
		// TODO
		return parent :: update_learning_object();
	}
}

?>