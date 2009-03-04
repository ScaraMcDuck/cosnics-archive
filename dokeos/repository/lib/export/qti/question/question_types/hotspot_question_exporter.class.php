<?php
require_once dirname(__FILE__).'/../question_qti_export.class.php';

class HotspotQuestionQtiExport extends QuestionQtiExport
{
	
	function export_learning_object()
	{
		$question = $this->get_learning_object();
		$answers = $question->get_answers();
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = $this->get_response_xml($answers);
		$item_xml[] = $this->get_outcome_xml();
		$item_xml[] = $this->get_interaction_xml($answers);
		//$item_xml[] = $this->get_response_processing_xml($answers);
		$item_xml[] = '</assessmentItem>';
		$file = parent :: create_qti_file(implode('', $item_xml));

		return $file;
	}
	
	function get_outcome_xml()
	{
		$outcome_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer"/>';

		return implode('', $outcome_xml);
	}
	
	function get_response_xml($answers)
	{
		$response_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="ordered" baseType="identifier">';
		$response_xml[] = '<correctResponse>';
		foreach ($answers as $i => $answer)
		{
			$response_xml[] = '<value>A'.$i.'</value>';
		}
		$response_xml[] = '</correctResponse>';
		$response_xml[] = '</responseDeclaration>';

		return implode('', $response_xml);
	}
	
	function get_interaction_xml($answers)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<p>'.htmlspecialchars($this->get_learning_object()->get_description()).'</p>';
		//add answers 

			$interaction_xml[] = '<graphicOrderInteraction responseIdentifier="RESPONSE" >';
			$interaction_xml[] = '<prompt></prompt>';
			
			$image = $this->get_learning_object()->get_image();
			$interaction_xml[] = '<object type="image" data="images/"></object>';
			foreach ($answers as $i => $answer)
			{
				$coords = $answer->get_hotspot_coordinates();
				$interaction_xml[] = '<hotspotChoice shape="'.$answer->get_hotspot_type().'" coords="'.$coords.'" identifier="A'.$i.'" />';
			}
			$interaction_xml[] = '</graphicOrderInteraction>';

		$interaction_xml[] = '</itemBody>';
		return implode('', $interaction_xml);
	}
}
?>