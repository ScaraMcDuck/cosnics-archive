<?php

require_once dirname(__FILE__).'/../question_result_display.class.php';

class HotspotQuestionResultDisplay extends QuestionResultDisplay
{

	function display_question_result()
	{
		$question = $this->get_question();
		$question_id = $this->get_clo_question()->get_id();
		$answers = $question->get_answers();
		
		$image_object = $question->get_image_object();
		$dimensions = getimagesize($image_object->get_full_path());
		$html[] = '<div id="hotspot_container_' . $question_id . '" class="hotspot_container"><div id="hotspot_image_' . $question_id . '" class="hotspot_image" style="width: '. $dimensions[0] .'px; height: '. $dimensions[1] .'px; background-image: url('. $image_object->get_url() .')"></div></div>';
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js');
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question_result_display.js');
		
        foreach($answers as $i => $answer)
        {
        	$html[] = '<input type="hidden" name="coordinates[' . $i . ']" value="' . $answer->get_hotspot_coordinates() . '" />';
        }
		
        $html[] = '<div class="clear"></div>';
        
		echo implode("\n", $html);
	}
	
	function add_borders()
	{
		return true;
	}
		
}
?>