<?php

require_once dirname(__FILE__).'/../question_result.class.php';

class HotspotQuestionResult extends QuestionResult
{
	function display_exercise()
	{
		$this->display_question_header();

		$results = parent :: get_results();
		$rdm = RepositoryDataManager :: get_instance();

		$question = parent :: get_question();
		$answers = $question->get_answers();
		
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result) 
		{
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		
		//print_r($results);
		//$this->display_answers($answer_lines, $correct_answer_lines);
		$clo_question = $this->get_clo_question();
		$pid = $this->get_user_assessment_id();
		$params = '?modifyAnswers=' . $clo_question->get_id().'&exe_id='.$pid;
		$hotspot_path = Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/hotspot_solution.swf';
		$answer_lines = array('
			<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/JavaScriptFlashGateway.js" ></script>
			<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/hotspot.js" ></script>
			<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/jsmethods.js" ></script>
			<script type="text/vbscript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/vbmethods.vbscript" ></script>
			<script type="text/javascript" >		
				var requiredMajorVersion = 7;
				var requiredMinorVersion = 0;
				var requiredRevision = 0;
				//var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
				var hasRequestedVersion = true;
				// Check to see if the version meets the requirements for playback
				if (hasRequestedVersion) {  // if weve detected an acceptable version
				    var oeTags = \'<object type="application/x-shockwave-flash" data="'.$hotspot_path.$params.'" width="720" height="650">\'
								+ \'<param name="movie" value="'.$hotspot_path.$params.'" />\'
								//+ \'<param name="test" value="OOoowww fo shooww" />\'
								+ \'</object>\';
				    document.write(oeTags);   // embed the Flash Content SWF when all tests are passed
				} else {  // flash is too old or we can\'t detect the plugin
					var alternateContent = "Error<br \/>"
						+ \'This content requires the Macromedia Flash Player.<br \/>\'
						+ \'<a href="http://www.macromedia.com/go/getflash/">Get Flash<\/a>\';
					document.write(alternateContent);  // insert non-flash content
				}
			</script>');
		
		$this->display_answers($answer_lines);//, $correct_answer_lines);
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_footer();
	}
	
	function display_survey()
	{
		$this->display_question_header();

		$results = parent :: get_results();
		$rdm = RepositoryDataManager :: get_instance();

		foreach ($results as $result)
		{
			$answer_lines[] = $result->get_extra();
		}
		$this->display_answers($answer_lines);
		$this->display_footer();
	}
	
	function display_assignment()
	{
		$this->display_question_header();

		$results = parent :: get_results();
		$rdm = RepositoryDataManager :: get_instance();

		$question = parent :: get_question();
		$answers = $question->get_answers();
		
		foreach ($answers as $answer)
		{
			$total_div += $answer->get_weight();
		}
		
		foreach ($results as $result) 
		{
			$total_score += $result->get_score();
		}
		
		$total_score = $total_score / $total_div * $this->get_clo_question()->get_weight();
		$total_div = $this->get_clo_question()->get_weight();
		$score_line = Translation :: get('Score').': '.$total_score.'/'.$total_div;
		
		//print_r($results);
		//$this->display_answers($answer_lines, $correct_answer_lines);
		$pid = $this->get_user_assessment_id();
		$params = '?modifyAnswers=' . $question->get_id().'&exe_id='.$pid;
		$hotspot_path = Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/hotspot_solution.swf';
		$answer_lines = array('
			<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/JavaScriptFlashGateway.js" ></script>
			<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/hotspot.js" ></script>
			<script type="text/javascript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/jsmethods.js" ></script>
			<script type="text/vbscript" src="'.Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/vbmethods.vbscript" ></script>
			<script type="text/javascript" >		
				var requiredMajorVersion = 7;
				var requiredMinorVersion = 0;
				var requiredRevision = 0;
				//var hasRequestedVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);
				var hasRequestedVersion = true;
				// Check to see if the version meets the requirements for playback
				if (hasRequestedVersion) {  // if weve detected an acceptable version
				    var oeTags = \'<object type="application/x-shockwave-flash" data="'.$hotspot_path.$params.'" width="720" height="650">\'
								+ \'<param name="movie" value="'.$hotspot_path.$params.'" />\'
								//+ \'<param name="test" value="OOoowww fo shooww" />\'
								+ \'</object>\';
				    document.write(oeTags);   // embed the Flash Content SWF when all tests are passed
				} else {  // flash is too old or we can\'t detect the plugin
					var alternateContent = "Error<br \/>"
						+ \'This content requires the Macromedia Flash Player.<br \/>\'
						+ \'<a href="http://www.macromedia.com/go/getflash/">Get Flash<\/a>\';
					document.write(alternateContent);  // insert non-flash content
				}
			</script>');
		
		$this->display_answers($answer_lines);//, $correct_answer_lines);
		
		$this->display_score($score_line);
		$this->display_feedback();
		
		if ($this->get_edit_rights() == 1 && $feedback = $_GET[AssessmentTool :: PARAM_ADD_FEEDBACK] == '1')
			$this->add_feedback_controls();
			
		$this->display_footer();
	}
}
?>