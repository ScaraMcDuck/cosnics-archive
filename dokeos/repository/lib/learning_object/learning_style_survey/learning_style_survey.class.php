<?php

require_once dirname(__FILE__) . '/../../learningobject.class.php';

/**
 * @author Tim De Pauw
 */
class LearningStyleSurvey extends LearningObject
{
	const PROPERTY_SURVEY_TYPE = 'survey_type';

	const SURVEY_TYPE_PROPOSITION_AGREEMENT = 1;
	const SURVEY_TYPE_ANSWER_ORDERING = 2;
	
	// TODO: Separate classes
	// TODO: Make each parameter an object, so it can be requiredl, have a type, ...
	private static $PARAMETER_NAMES = array(
		self :: SURVEY_TYPE_PROPOSITION_AGREEMENT => array(
			'first_percentile_end',
			'second_percentile_end',
			'third_percentile_end'
			// TODO
		),
		self :: SURVEY_TYPE_ANSWER_ORDERING => array(
			// TODO
		)
	); 
	
	function get_survey_type ()
	{
		return $this->get_additional_property(self :: PROPERTY_SURVEY_TYPE);
	}
	
	function set_survey_type ($type)
	{
		return $this->set_additional_property(self :: PROPERTY_SURVEY_TYPE, $type);
	}
	
	function is_versionable()
	{
		return false;
	}
	
	function get_survey_parameter_names()
	{
		return self :: get_survey_type_parameter_names($this->get_survey_type());
	}
	
	static function get_survey_type_parameter_names($type)
	{
		return self :: $PARAMETER_NAMES[$type];
	}
	
	static function get_available_survey_types()
	{
		return array(
			self :: SURVEY_TYPE_PROPOSITION_AGREEMENT => get_lang('PropositionAgreementSurvey'),
			self :: SURVEY_TYPE_ANSWER_ORDERING => get_lang('AnswerOrderingSurvey')
		);
	}
}

?>