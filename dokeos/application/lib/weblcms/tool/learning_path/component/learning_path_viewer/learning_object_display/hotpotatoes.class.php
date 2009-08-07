<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class HotpotatoesDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($hp, $tracker_attempt_data)
	{
		$lpi_attempt_id = $tracker_attempt_data['active_tracker']->get_id();
		
		$link = $hp->add_javascript(Path :: get(WEB_PATH) . 'application/lib/weblcms/ajax/lp_hotpotatoes_save_score.php', null, $lpi_attempt_id);
		$html[] = $this->display_link($link);
		
		return implode("\n", $html);
	}
}

?>