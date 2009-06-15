<?php
class HotspotQuestionDisplay extends QuestionDisplay
{
	function add_question_form($formvalidator)
	{
		$clo_question = $this->get_clo_question();
		$question = RepositoryDataManager :: get_instance()->retrieve_learning_object($clo_question->get_ref());
		$this->add_scripts_element($clo_question->get_id(), $formvalidator);
		//$formvalidator->addElement('html', '<br/>');
		$answers = $question->get_answers();
		foreach ($answers as $i => $answer)
		{
			$formvalidator->addElement('hidden', $clo_question->get_id().'_'.$i, '', array('id' => $clo_question->get_id().'_'.$i));
		}
	}
	
	function add_scripts_element($hotspot_id, $formvalidator)
	{
		$hotspot_path = Path :: get(WEB_PLUGIN_PATH).'hotspot/hotspot/hotspot_user.swf';
		//dump($hotspot_path);
		return $formvalidator->addElement('html','
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
				    var oeTags = \'<object type="application/x-shockwave-flash" data="'.$hotspot_path.'?modifyAnswers=' . $hotspot_id.'" width="720" height="650">\'
								+ \'<param name="movie" value="'.$hotspot_path.'?modifyAnswers=' . $hotspot_id.'" />\'
								//+ \'<param name="test" value="OOoowww fo shooww" />\'
								+ \'</object>\';
				    document.write(oeTags);   // embed the Flash Content SWF when all tests are passed
				} else {  // flash is too old or we can\'t detect the plugin
					var alternateContent = "Error<br \/>"
						+ \'This content requires the Macromedia Flash Player.<br \/>\'
						+ \'<a href="http://www.macromedia.com/go/getflash/">Get Flash<\/a>\';
					document.write(alternateContent);  // insert non-flash content
				}
			</script>'
		);
	}
	
	function get_instruction()
	{
		
	}
}
?>