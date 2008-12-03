<?php
require_once dirname(__FILE__).'/../qti_export.class.php';
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';

class AssessmentQtiExport extends QtiExport
{
	//private $assessment;
	
	function AssessmentQtiExport($assessment)
	{
		//$this->assessment = $assessment;
		parent :: __construct($assessment);
	}
	
	function export_learning_object()
	{
		$rdm = RepositoryDataManager :: get_instance();
		$assessment = $this->get_learning_object();
		$assessment_xml[] = $this->get_assessment_xml_header($assessment);
		//print_r($assessment_xml);
		$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $assessment->get_id());
		$clo_questions = $rdm->retrieve_complex_learning_object_items($condition);
		while ($clo_question = $clo_questions->next_result())
		{
			$question = $rdm->retrieve_learning_object($clo_question->get_ref());
			$question_exporter = QtiExport :: factory_qti($question);
			//export question
			$filename = $question_exporter->export_learning_object();
			$question_files[] = $filename;
			$shortfilename = split('/', $filename);
			$assessment_xml[] = '<assessmentItemRef identifier="'.$question->get_id().'" href="'.$shortfilename[count($shortfilename)-1].'" />';
		}
		$assessment_xml[] = $this->get_assessment_xml_footer();
		//print_r(implode('', $assessment_xml));
		
		$path = $this->createdoc(implode('', $assessment_xml));
		
		return $path;
	}
	
	function createdoc($assessment_xml)
	{
		$doc = new DOMDocument();
		$doc->loadXML($assessment_xml);
		//echo $doc->saveXML();
		
		$temp_dir = Path :: get(SYS_TEMP_PATH). $this->get_learning_object()->get_owner_id() . '/export_qti/';
  		
  		if(!is_dir($temp_dir))
  		{
  			mkdir($temp_dir, '0777', true);
  		}
  	
  		$xml_path = $temp_dir . 'qti_'.$this->get_learning_object()->get_id().'.xml';
		$doc->save($xml_path);
		
		$zip = Filecompression :: factory();
		$zippath = $zip->create_archive($temp_dir);
		FileSystem::remove($temp_dir);
			
		return $zippath;
	}
	
	function get_assessment_xml_header($assessment)
	{
		$header[] = '<?xml version="1.0" encoding="UTF-8" ?>';
		$header[] = '<assessmentTest xmlns="http://www.imsglobal.org/xsd/imsqti_v2p1" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.imsglobal.org/xsd/imsqti_v2p1
			http://www.imsglobal.org/xsd/imsqti_v2p1.xsd" identifier="a'.$assessment->get_id().'" title="'.$assessment->get_title().'">';
 		$header[] = '<testPart identifier="P1" navigationMode="linear" submissionMode="individual">';
 		$header[] = '<assessmentSection identifier="set" title="'.htmlspecialchars($assessment->get_description()).'" visible="true">';
 		//print_r ($header);
  		return implode('', $header);
	}
	
	function get_assessment_xml_footer()
	{
		$footer[] = '</assessmentSection>';
  		$footer[] = '</testPart>';
  		$footer[] = '</assessmentTest>';
  		//print_r($footer);
  		return implode('', $footer);
	}
	
	 
 
}
?>