<?php
/**
 * $Id: announcement.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * This class represents an open question
 */
class Hotpotatoes extends LearningObject
{
	const PROPERTY_PATH = 'path';
	const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';
	const PROPERTY_QUESTIONS_PER_PAGE = 'questions_per_page';
	
	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_PATH, self :: PROPERTY_MAXIMUM_ATTEMPTS, self :: PROPERTY_QUESTIONS_PER_PAGE);
	}
	
	const TYPE_HOTPOTATOES = 'hotpotatoes';
	
	function get_assessment_type()
	{
		return self :: TYPE_HOTPOTATOES;
	}
	
	function get_times_taken() 
	{
		return WeblcmsDataManager :: get_instance()->get_num_user_assessments($this);
	}
	
	function get_average_score()
	{
		return WeblcmsDataManager :: get_instance()->get_average_score($this);
	}
	
	function get_maximum_score()
	{
		return WeblcmsDataManager :: get_instance()->get_maximum_score($this);
	}
	
	function get_questions_per_page()
	{
		return $this->get_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE);
	}
	
	function set_questions_per_page($value)
	{
		$this->set_additional_property(self :: PROPERTY_QUESTIONS_PER_PAGE, $value);
	}
	
	function get_maximum_attempts()
	{
		return $this->get_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS);
	}
	
	function set_maximum_attempts($value)
	{
		$this->set_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS, $value);
	}
	
	function get_path()
	{
		return $this->get_additional_property(self :: PROPERTY_PATH);
	}
	
	function set_path($path)
	{
		return $this->set_additional_property(self :: PROPERTY_PATH, $path);
	}
	
	function delete()
	{
		$this->delete_file();
		parent :: delete();
	}
	
	function delete_file()
	{
		$path = Path :: get(SYS_REPO_PATH) . $this->get_path();
		Filesystem::remove($path);
	}
	
	function add_javascript()
	{
		$content = $this->read_file_content();
		$js_content = $this->replace_javascript($content);
		$this->write_file_content($js_content);
	}
	
	private function read_file_content()
	{
		$full_file_path = Path :: get(SYS_REPO_PATH) . $this->get_path();
		
		if(is_file($full_file_path)) 
		{
			if (!($fp = fopen(urldecode($full_file_path), "r"))) 
			{
				return "";
			}
			$contents = fread($fp, filesize($full_file_path));
			fclose($fp);
			return $contents;
	  	}
	}
	
	private function write_file_content($content)
	{
		$full_file_path = Path :: get(SYS_REPO_PATH) . substr($this->get_path(), 0, strlen($this->get_path()) - 4) . '.' . Session :: get_user_id() . '.t.htm';
		Filesystem::remove($full_file_path);
		
		if (($fp = fopen(urldecode($full_file_path), "w"))) 
		{
			fwrite($fp,$content);
			fclose($fp);
		}
	}
	
	private function replace_javascript($content)
	{
		$mit = "function Finish(){";

		$js_content = "var SaveScoreVariable = 0; // This variable included by Dokeos System\n".
					"function mySaveScore() // This function included by Dokeos System\n".
					"{\n".
					"   if (SaveScoreVariable==0)\n".
					"		{\n".
					"			SaveScoreVariable = 1;\n".
					"			if (C.ie)\n".
					"			{\n".
				//	"				//document.location.href = \"".Path :: get(WEB_PATH)."main/exercice/"."savescores.php?origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id&time=$time&test=".$doc_url."&uid=".$_user['user_id']."&cid=".$cid."&score=\"+Score;\n".
					"				window.alert(Score);\n".
					"			}\n".
					"			else\n".
					"			{\n".
					"				window.alert(Score);\n".
				//	"				//window.location.href = \"".Path :: get(WEB_PATH)."main/exercice/"."savescores.php?origin=$origin&learnpath_id=$learnpath_id&learnpath_item_id=$learnpath_item_id&time=$time&test=".$doc_url."&uid=".$_user['user_id']."&cid=".$cid."&score=\"+Score;\n".
					"			}\n".
					"		}\n".
					"}\n".
					"// Must be included \n".
					"function Finish(){\n".
					" mySaveScore();";
		
		$newcontent = str_replace($mit,$js_content,$content);
		$prehref="<!-- BeginTopNavButtons -->";
		$posthref="<!-- BeginTopNavButtons --><!-- edited by Dokeos -->";
		$newcontent = str_replace($prehref,$posthref,$newcontent);
		return $newcontent;
	}
}
?>