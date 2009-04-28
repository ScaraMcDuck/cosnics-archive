<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class ScormItemDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($scorm_item)
	{	
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_APP_PATH) . 'lib/weblcms/tool/learning_path/javascript/scorm/dokeos_api.js');
		$html[] = $this->display_link($scorm_item->get_url(true));
			
		return implode("\n", $html);
	}
}

?>