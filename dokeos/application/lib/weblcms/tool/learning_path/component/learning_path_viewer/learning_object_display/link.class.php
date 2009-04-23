<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class LinkDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($link)
	{
		$html[] = $this->add_tracking_javascript();
		//$html[] = '<h3>' . $link->get_title() . '</h3>' . $link->get_description() . '<br />';
		$html[] = $this->display_link($link->get_url());
		
		return implode("\n", $html);
	}
}

?>