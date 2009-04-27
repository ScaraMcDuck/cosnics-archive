<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class ScormItemDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($document)
	{	
		$html[] = $this->display_link($document->get_url());
			
		return implode("\n", $html);
	}
}

?>