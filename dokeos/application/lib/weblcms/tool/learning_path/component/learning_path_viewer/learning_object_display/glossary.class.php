<?php

require_once dirname(__FILE__) . '/../learning_path_content_object_display.class.php';

class GlossaryDisplay extends LearningPathContentObjectDisplay
{
	function display_content_object($glossary)
	{
		$html[] = $this->add_tracking_javascript();
		$link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'pid' => $glossary->get_id()));
		$html[] = $this->display_link($link);
		
		return implode("\n", $html);
	}
}

?>