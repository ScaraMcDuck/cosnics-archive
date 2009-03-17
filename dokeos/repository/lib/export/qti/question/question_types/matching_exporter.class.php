<?php
require_once dirname(__FILE__).'/../question_qti_export.class.php';

class MatchingQuestionQtiExport extends QuestionQtiExport
{
	
	function export_learning_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $this->get_learning_object();

		$q_answers = $question->get_options();
		$q_matches = $question->get_matches();
		foreach($q_answers as $q_answer)
		{
			$match = $q_matches[$q_answer->get_match()]; 
			$answers[] = array('answer' => $q_answer->get_value(), 'matchnum' => $q_answer->get_match(), 'match' => $match, 'score' => $q_answer->get_weight());
		}
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="directedPair">';
		$item_xml[] = $this->get_response_xml($answers);
		$item_xml[] = '</responseDeclaration>';

		$item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" />';
		$item_xml[] = $this->get_interaction_xml($answers, $q_matches);
		$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response" />';
		$item_xml[] = '</assessmentItem>';
		return parent :: create_qti_file(implode('', $item_xml));
	}
	
	function get_response_xml($answers)
	{
		$response_xml[] = '<correctResponse>';
		foreach ($answers as $i => $answer)
		{
			if ($answer['score'] > 0)
				$response_xml[] = '<value>c'.$i.' m'.$answer['matchnum'].'</value>';
		}
		$response_xml[] = '</correctResponse>';
		$response_xml[] = '<mapping defaultValue="0">';
		foreach ($answers as $i => $answer)
		{
			$response_xml[] = '<mapEntry mapKey="c'.$i.' m'.$answer['matchnum'].'" mappedValue="'.$answer['score'].'" />';
		}
		$response_xml[] = '</mapping>';
		
		return implode('', $response_xml);
	}
	
	function get_interaction_xml($answers, $matches)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<matchInteraction responseIdentifier="RESPONSE" shuffle="true" maxAssociations="'.sizeof($answers).'">';
		$interaction_xml[] = '<prompt>'.include_question_images($this->get_learning_object()->get_description()).'</prompt>';
		$interaction_xml[] = '<simpleMatchSet>';
		foreach ($answers as $i => $answer)
		{
			$interaction_xml[] = '<simpleAssociableChoice identifier="c'.$i.'" matchMax="1">'.htmlspecialchars($answer['answer']).'</simpleAssociableChoice>';
		}
		$interaction_xml[] = '</simpleMatchSet>';
		$interaction_xml[] = '<simpleMatchSet>';

		foreach ($matches as $i => $match)
		{
			$interaction_xml[] = '<simpleAssociableChoice identifier="m'.$i.'" matchMax="'.sizeof($answers).'">'.htmlspecialchars($match).'</simpleAssociableChoice>';
		}
		$interaction_xml[] = '</simpleMatchSet>';
		$interaction_xml[] = '</matchInteraction>';
		$interaction_xml[] = '</itemBody>';
		
		return implode('', $interaction_xml);
	}
	
	/*function create_match_list($answers)
	{
		foreach ($answers as $answer)
		{
			$exists = false;
			$match = $answer['match'];
			foreach ($matches as $ex_match)
			{
				if ($ex_match == $match)
					$exists = true;
			}
			if (!$exists) 
			{
				$matches[] = $match;
			}
		}
		return $matches;
	}*/
}
?>