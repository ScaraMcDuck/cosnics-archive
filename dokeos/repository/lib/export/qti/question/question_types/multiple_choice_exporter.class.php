<?php
require_once dirname(__FILE__).'/../question_exporter.class.php';

class MultipleChoiceQuestionQtiExport extends QuestionQtiExport
{
	
	function export_learning_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $this->get_learning_object();
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $question->get_id());
		$clo_answers = $rdm->retrieve_complex_learning_object_items($condition);
		while ($clo_answer = $clo_answers->next_result())
		{
			$answer = $rdm->retrieve_learning_object($clo_answer->get_ref(), 'answer');
			$answers[] = array('answer' => $answer, 'score' => $clo_answer->get_score());
		}
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="identifier">';
		$item_xml[] = $this->get_response_xml($answers);
		$item_xml[] = '</responseDeclaration>';
		$item_xml[] = $this->get_outcome_xml($answers);
		$item_xml[] = $this->get_interaction_xml($answers);
		$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
		$item_xml[] = '</assessmentItem>';
		return parent :: create_qti_file(implode('', $item_xml));
	}
	
	function get_response_xml($answers)
	{
		$response_xml[] = '<correctResponse>';
		foreach ($answers as $answer)
		{
			if ($answer['score'] > 0)
				$response_xml[] = '<value>c'.$answer['answer']->get_id().'</value>';
		}
		$response_xml[] = '</correctResponse>';
		
		return implode('', $response_xml);
	}
	
	function get_outcome_xml($answers)
	{
		$outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer">';
		$outcome_xml[] = '<defaultValue>';
		$outcome_xml[] = '<value>0</value>';
		$outcome_xml[] = '</defaultValue>';
		$outcome_xml[] = '</outcomeDeclaration>';
		return implode('', $outcome_xml);
	}
	
	function get_interaction_xml($answers)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<choiceInteraction responseIdentifier="RESPONSE" shuffle="false" maxChoices="1">';
		$interaction_xml[] = '<prompt>'.htmlspecialchars($this->get_learning_object()->get_description()).'</prompt>';
		foreach ($answers as $answer)
		{
			$interaction_xml[] = '<simpleChoice identifier="c'.$answer['answer']->get_id().'" fixed="false">'.htmlspecialchars($answer['answer']->get_title()).'</simpleChoice>';
		}
		$interaction_xml[] = '</choiceInteraction>';
		$interaction_xml[] = '</itemBody>';
		
		return implode('', $interaction_xml);
	}
}
?>