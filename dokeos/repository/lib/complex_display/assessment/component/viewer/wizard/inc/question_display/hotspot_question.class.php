<?php
class HotspotQuestionDisplay extends QuestionDisplay
{
	private $colours = array('#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893');

	function add_question_form()
	{
		$formvalidator = $this->get_formvalidator();
		$clo_question = $this->get_clo_question();
		$question = $this->get_question();
		$answers = $this->shuffle_with_keys($question->get_answers());
        $renderer = $this->get_renderer();

        $image_html = array();
		$image_object = $question->get_image_object();
		$dimensions = getimagesize($image_object->get_full_path());
		$image_html[] = '<div class="description_hotspot">';
		$image_html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: '. $dimensions[0] .'px; height: '. $dimensions[1] .'px; background-image: url('. $image_object->get_url() .')"></div></div>';
		$image_html[] = '<div class="clear"></div>';
		$image_html[] = '<div id="hotspot_marking"><div class="colour_box_label">' . Translation :: get('CurrentlyMarking') . '</div><div class="colour_box"></div></div>';
		$image_html[] = '<div class="clear"></div>';
		$image_html[] = '</div>';
		$formvalidator->addElement('html', implode("\n", $image_html));

        $table_header = array();
        $table_header[] = '<table class="data_table take_assessment">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="checkbox"></th>';
        $table_header[] = '<th>' . $this->get_instruction() . '</th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $formvalidator->addElement('html', implode("\n", $table_header));

        $question_id = $clo_question->get_id();

        foreach ($answers as $i => $answer)
        {
        	$answer_name = $question_id . '_' . $i;

            $group = array();
            $group[] = $formvalidator->createElement('static', null, null, '<div class="colour_box" style="background-color: ' . $this->colours[$i] . ';"></div>');
            $group[] = $formvalidator->createElement('static', null, null, $answer->get_answer());
            $group[] = $formvalidator->createElement('hidden', $answer_name, '');

            $formvalidator->addGroup($group, 'option_' . $i, null, '', false);

            $renderer->setElementTemplate('<tr class="' . ($i % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $i);
            $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $i);
        }

        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $formvalidator->addElement('html', implode("\n", $table_footer));

//		$this->add_scripts_element($clo_question->get_id(), $formvalidator);
//		//$formvalidator->addElement('html', '<br/>');
//		$answers = $question->get_answers();
//		foreach ($answers as $i => $answer)
//		{
//			$formvalidator->addElement('hidden', $clo_question->get_id().'_'.$i, '', array('id' => $clo_question->get_id().'_'.$i));
//		}
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
		return Translation :: get('MarkHotspots');
	}
}
?>