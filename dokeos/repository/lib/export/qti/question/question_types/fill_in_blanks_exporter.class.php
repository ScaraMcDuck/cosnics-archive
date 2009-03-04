<?php
require_once dirname(__FILE__).'/../question_qti_export.class.php';

class FillInBlanksQuestionQtiExport extends QuestionQtiExport
{
	
	function export_learning_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $this->get_learning_object();
		$answers = $question->get_answers();
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = $this->get_response_xml($answers);
		$item_xml[] = $this->get_outcome_xml();
		$item_xml[] = $this->get_interaction_xml($answers);
		$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/match_correct" />';
		$item_xml[] = '</assessmentItem>';
		$file = parent :: create_qti_file(implode('', $item_xml));
		//echo(implode('', $item_xml));
		//echo($file);
		return $file;
	}
	
	function get_outcome_xml()
	{
		$outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float">';
		//$outcome_xml[] = '<outcomeDeclaration identifier="FEEDBACK" cardinality="single" baseType="identifier">';
		$outcome_xml[] = '<defaultValue>';
		$outcome_xml[] = '<value>0</value>';
		$outcome_xml[] = '</defaultValue>';
		$outcome_xml[] = '</outcomeDeclaration>';
		return implode('', $outcome_xml);
	}
	
	function get_response_xml($answers)
	{
		foreach($answers as $i => $answer)
		{
			$response_xml[] = '<responseDeclaration identifier="c'.$i.'" cardinality="single" baseType="string">';
			$response_xml[] = '<correctResponse>';
			$response_xml[] = '<value>'.htmlspecialchars($answer->get_value()).'</value>';
			$response_xml[] = '<mapping defaultValue="0">';
			$response_xml[] = '<mapEntry mapKey="'.htmlspecialchars($answer->get_value()).'" mappedValue="'.$answer->get_weight().'"/>';
			$response_xml[] = '</mapping>';
			$response_xml[] = '</correctResponse>';
			$response_xml[] = '</responseDeclaration>';
		}
		return implode('', $response_xml);
	}
	
	function get_interaction_xml($answers)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<prompt>'.htmlspecialchars($this->get_learning_object()->get_description()).'</prompt>';
		foreach ($answers as $i => $answer)
		{
			$interaction_xml[] = '<textEntryInteraction responseIdentifier="c'.$i.'" expectedLength="20">';
			//$interaction_xml[] = '<feedbackInline outcomeIdentifier="'..'" identifier="MGH001A" showHide="show">No, he is the President of the USA.</feedbackInline>';
			$interaction_xml[] = '</textEntryInteraction>';
		}

		$interaction_xml[] = '</itemBody>';
		
		return implode('', $interaction_xml);
	}
}
?>