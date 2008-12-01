<?php
require_once dirname(__FILE__).'/../question_exporter.class.php';

class MatchingQuestionQtiExport extends QuestionQtiExport
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
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $answer->get_id());
			$clo_match = $rdm->retrieve_complex_learning_object_items($condition)->next_result();
			$match = $rdm->retrieve_learning_object($clo_match->get_ref());
			$answers[] = array('answer' => $answer, 'match' => $match, 'score' => $clo_answer->get_score());
		}
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directedPair">';
		$item_xml[] = $this->get_response_xml($answers);
		$item_xml[] = '</responseDeclaration>';

		$item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" />';
		$item_xml[] = $this->get_interaction_xml($answers);
		$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response" />';
		$item_xml[] = '</assessmentItem>';
		return implode('', $item_xml);
	}
	
	function get_response_xml($answers)
	{
		$response_xml[] = '<correctResponse>';
		foreach ($answers as $answer)
		{
			if ($answer['score'] > 0)
				$response_xml[] = '<value>'.$answer['answer']->get_id().' '.$answer['match']->get_id().'</value>';
		}
		$response_xml[] = '</correctResponse>';
		$response_xml[] = '<mapping defaultValue="0">';
		foreach ($answers as $answer)
		{
			$response_xml[] = '<mapEntry mapKey="'.$answer['answer']->get_id().' '.$answer['match']->get_id().'" mappedValue="'.$answer['score'].'" />';
		}
		$response_xml[] = '</mapping>';
		
		return implode('', $response_xml);
	}
	
	function get_interaction_xml($answers)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<matchInteraction responseIdentifier="RESPONSE" shuffle="true" maxAssociations="'.sizeof($answers).'">';
		$interaction_xml[] = '<prompt>'.htmlspecialchars($this->get_learning_object()->get_description()).'</prompt>';
		$interaction_xml[] = '<simpleMatchSet>';
		foreach ($answers as $answer)
		{
			$interaction_xml[] = '<simpleAssociableChoice identifier="'.$answer['answer']->get_id().'" matchMax="1">'.htmlspecialchars($answer['answer']->get_title()).'</simpleAssociableChoice>';
		}
		$interaction_xml[] = '</simpleMatchSet>';
		$interaction_xml[] = '<simpleMatchSet>';
		$matches = $this->create_match_list($answers);
		foreach ($matches as $match)
		{
			$interaction_xml[] = '<simpleAssociableChoice identifier="'.$match->get_id().'" matchMax="'.sizeof($answers).'">'.htmlspecialchars($match->get_title()).'</simpleAssociableChoice>';
		}
		$interaction_xml[] = '</simpleMatchSet>';
		$interaction_xml[] = '</matchInteraction>';
		$interaction_xml[] = '</itemBody>';
		
		return implode('', $interaction_xml);
	}
	
	function create_match_list($answers)
	{
		foreach ($answers as $answer)
		{
			$exists = false;
			$match = $answer['match'];
			foreach ($matches as $ex_match)
			{
				if ($ex_match->get_id() == $match->get_id())
					$exists = true;
			}
			if (!$exists) 
			{
				$matches[] = $match;
			}
		}
		return $matches;
	}
}
?>