<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class WikiDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($wiki)
	{
		$html[] = $this->add_tracking_javascript();
        $hp_cloi = WikiTool :: get_wiki_homepage($wiki->get_id());
        if(!empty($hp_cloi))
        $link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'display_action' => 'view_item', 'pid' => $wiki->get_id(), 'selected_cloi' => $hp_cloi->get_id()));
        else
		$link = $this->get_parent()->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_VIEW_CLO, 'pid' => $wiki->get_id()));
		$html[] = $this->display_link($link);
		
		return implode("\n", $html);
	}
}

?>