<?php
require_once dirname(__FILE__).'/../question_qti_export.class.php';

class MultipleAnswerQuestionQtiExport extends QuestionQtiExport
{
	
	function export_learning_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$question = $this->get_learning_object();
		$q_answers = $question->get_options();
		foreach($q_answers as $q_answer)
		{
			$answers[] = array('answer' => $q_answer->get_value(), 'score' => $q_answer->get_weight(), 'feedback' => $q_answer->get_comment());
		}
		
		$item_xml[] = '<assessmentItem xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1    http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="q'.$question->get_id().'" title="'.$question->get_title().'" adaptive="false" timeDependent="false">';
		$item_xml[] = '<responseDeclaration identifier="RESPONSE" cardinality="multiple" baseType="identifier">';
		$item_xml[] = $this->get_response_xml($answers);
		$item_xml[] = '</responseDeclaration>';

		$item_xml[] = '<outcomeDeclaration identifier="SCORE" cardinality="single" baseType="float" />';
		$item_xml[] = '<outcomeDeclaration identifier="FEEDBACK" cardinality="multiple" baseType="identifier" />';
		$item_xml[] = $this->get_interaction_xml($answers);
		$item_xml[] = '<responseProcessing template="http://www.imsglobal.org/question/qti_v2p1/rptemplates/map_response" />';
		$item_xml[] = '</assessmentItem>';
		//dump(htmlspecialchars(implode('', $item_xml)));
		return parent :: create_qti_file(implode('', $item_xml));
	}
	
	function get_response_xml($answers)
	{
		$response_xml[] = '<correctResponse>';
		foreach ($answers as $i => $answer)
		{
			if ($answer['score'] > 0)
				$response_xml[] = '<value>c'.$i.'</value>';
		}
		$response_xml[] = '</correctResponse>';
		$response_xml[] = '<mapping>';
		foreach ($answers as $i => $answer)
		{
			$response_xml[] = '<mapEntry mapKey="c'.$i.'" mappedValue="'.$answer['score'].'" />';
		}
		$response_xml[] = '</mapping>';
		
		return implode('', $response_xml);
	}
	
	function get_interaction_xml($answers)
	{
		$interaction_xml[] = '<itemBody>';
		$interaction_xml[] = '<choiceInteraction responseIdentifier="RESPONSE" shuffle="true" maxChoices="0">';
		$interaction_xml[] = '<prompt>'.$this->include_question_images($this->get_learning_object()->get_description()).'</prompt>';
		foreach ($answers as $i => $answer)
		{
			$interaction_xml[] = '<simpleChoice identifier="c'.$i.'" fixed="false">'.$this->include_question_images($answer['answer']);
			$interaction_xml[] = '<feedbackInline outcomeIdentifier="FEEDBACK" identifier="c'.$i.'" showHide="show">'.$this->include_question_images($answer['comment']).'</feedbackInline>';
			$interaction_xml[] = '</simpleChoice>';
		}
		$interaction_xml[] = '</choiceInteraction>';
		$interaction_xml[] = '</itemBody>';
		
		return implode('', $interaction_xml);
	}
}
?>