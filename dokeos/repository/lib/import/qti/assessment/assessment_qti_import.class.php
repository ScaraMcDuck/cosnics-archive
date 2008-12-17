<?php
require_once Path :: get_plugin_path().'pear/XML/Unserializer.php';
require_once Path :: get_repository_path().'lib/learning_object/assessment/assessment.class.php';

class AssessmentQtiImport extends QtiImport
{
	
	function import_learning_object()
	{
		$data = $this->get_file_content_array();
		//echo '<br/>';
		//print_r($data);
		//echo '<br/>';
		
		$assessment = new Assessment();
		$title = $data['title'];
		$assessment->set_title($title);
		echo $title;
		$testparts = $data['testPart'];
		
		if ($testparts[0] != null)
		{
			//multiple test parts, probably never going to happen
		}
		else
		{
			//one test part
			$this->import_testpart($testparts, $assessment);
		}
	}
	
	function import_testpart($part, $assessment)
	{
		$assessment_sections = $part['assessmentSection'];
		
		if ($assessment_sections[0] != null)
		{
			//multiple assessment sections, probably never going to happen
		}
		else
		{
			//one assessment section
			$this->import_assessment_section($assessment_sections, $assessment);
		}
	}
	
	function import_assessment_section($assessment_section, $assessment)
	{
		$descr = $assessment_section['title'];
		echo $descr;
		$assessment->set_description($descr);
		
		$assessment_item_refs = $assessment_section['assessmentItemRef'];
		if ($assessment_item_refs[0] != null)
		{
			//multiple assessment itemrefs
			foreach ($assessment_item_refs as $item_ref)
			{
				$this->import_assessment_item_ref($item_ref, $assessment);
			}
		}
		else
		{
			//one assessment itemref
			$this->import_assessment_item_ref($assessment_item_refs, $assessment);
		}
	}
	
	function import_assessment_item_ref($item_ref, $assessment)
	{
		$item_ref_file = $item_ref['href'];
		
		$dirparts = split('/', $this->get_learning_object_file());
		for ($i = 0; $i < count($dirparts) -1; $i++)
		{
			$dir .= $dirparts[$i].'/';
		}
		echo '<br/>import question from '.$dir.$item_ref_file.'<br/>';
		$question_qti_import = QtiImport :: factory_qti($item_ref_file, $this->get_user(), $this->get_category(), $dir);
		$question_qti_import->import_learning_object();
	}
}
?>