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
		//add answers 

		$interaction_xml[] = '<graphicOrderInteraction responseIdentifier="RESPONSE" >';
		$interaction_xml[] = '<prompt>';
		$interaction_xml[] = '<p>'.$this->include_question_images($this->get_learning_object(), $this->get_learning_object()->get_description()).'</p>';
		$interaction_xml[] = '</prompt>';
		
		$image = $this->get_learning_object()->get_image();
		$parts = split('/', $image);
		$imagename = $parts[count($parts)-1];
		$parts = split('\.', $image);
		$extension = strtolower($parts[count($parts)-1]);
		$size = getimagesize(Path :: get(SYS_FILE_PATH).'repository/'.$image);
		
		$temp_dir = Path :: get(SYS_TEMP_PATH). $this->get_learning_object()->get_owner_id() . '/export_qti/images/'.$imagename;
		mkdir(Path :: get(SYS_TEMP_PATH). $this->get_learning_object()->get_owner_id() . '/export_qti/images/', null, true);
		copy(Path :: get(SYS_FILE_PATH).'repository/'.$image ,$temp_dir);

		$interaction_xml[] = '<object type="image/'.$extension.'" width="'.$size[0].'" height="'.$size[1].'" data="images/'.$imagename.'"></object>';
		foreach ($answers as $i => $answer)
		{
			$coords = $answer->get_hotspot_coordinates();
			$type = $answer->get_hotspot_type();
			$export_type = $this->export_type($type);
			$export_coords = $this->transform_coords($coords, $export_type);
			//dump($export_coords);
			$interaction_xml[] = '<hotspotChoice shape="'.$export_type.'" coords="'.$export_coords.'" identifier="A'.$i.'" />';
		}
		$interaction_xml[] = '</graphicOrderInteraction>';

		$interaction_xml[] = '</itemBody>';
		return implode('', $interaction_xml);
	}
	
	function export_type($type)
	{
		switch ($type)
		{
			case 'square':
				return 'rect';
			case 'circle':
				return 'ellipse';
			case 'poly':
				return 'poly';
			default:
				return '';
		}
	}
	
	function transform_coords($coords, $export_type)
	{
		switch ($export_type)
		{
			case 'rect':
				$coords = str_replace('|', ',', $coords);
				$coords = str_replace(';', ',', $coords);
				$parts = split(',', $coords);
				$points = $parts[0].','.$parts[1].','.($parts[2] + $parts[0]).','.($parts[3] + $parts[1]);
				return $points;
			case 'ellipse':
				$coords = str_replace('|', ',', $coords);
				$coords = str_replace(';', ',', $coords);
				return $coords;
			case 'poly':
				$coords = str_replace('|', ',', $coords);
				$coords = str_replace(';', ',', $coords);
				$parts = split(',', $coords);
				$coords .= ','.$parts[0].','.$parts[1];
				return $coords;
			default:
				return '';
		}
	}
}
?>