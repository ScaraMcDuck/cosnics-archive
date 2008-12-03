<?php
require_once dirname(__FILE__).'/../question_exporter.class.php';

class OpenQuestionWithDocumentQuestionQtiExport extends QuestionQtiExport
{
	
	function export_learning_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $this->get_learning_object();
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="single" baseType="file" />';

		$item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="integer" />';
		$item_xml[] = $this->get_interaction_xml();
		//$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response" />';
		$item_xml[] = '</assessmentItem>';
		return parent :: create_qti_file(implode('', $item_xml));
	}
	
	function get_interaction_xml()
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<uploadInteraction responseIdentifier="RESPONSE">';
		$interaction_xml[] = '<prompt>'.htmlspecialchars($this->get_learning_object()->get_description()).'</prompt>';

		$interaction_xml[] = '</uploadInteraction>';
		$interaction_xml[] = '</itemBody>';
		
		return implode('', $interaction_xml);
	}
}
?>