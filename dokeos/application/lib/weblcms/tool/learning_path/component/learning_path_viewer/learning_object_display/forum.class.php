<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class ForumDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($forum)
	{
		$html[] = $this->add_tracking_javascript();
		$link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'pid' => $forum->get_id()));
		$html[] = $this->display_link($link);
		
		return implode("\n", $html);
	}
}

?>