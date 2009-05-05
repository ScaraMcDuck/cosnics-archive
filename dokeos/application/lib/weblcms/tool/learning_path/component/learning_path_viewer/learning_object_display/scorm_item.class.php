<?php

require_once dirname(__FILE__) . '/../learning_path_learning_object_display.class.php';

class ScormItemDisplay extends LearningPathLearningObjectDisplay
{
	function display_learning_object($scorm_item, $tracker_attempt_data, $navigation)
	{	
		//dump($tracker_attempt_data);
		if($tracker_attempt_data['active_tracker'])
		{
			$id = $tracker_attempt_data['active_tracker']->get_id();
			$tracker_attempt_data['active_tracker']->set_start_time(time());
			$tracker_attempt_data['active_tracker']->update();
		}
		
		$html[] = '<script language="JavaScript">var tracker_id = ' . $id;
		$html[] = 'var continue_url = "' . $navigation['continue_url'] . '";';
		$html[] = 'var previous_url = "' . $navigation['previous_url'] . '";';  
		$html[] = '</script>';
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_APP_PATH) . 'lib/weblcms/tool/learning_path/javascript/scorm/dokeos_api.js');
		$html[] = $this->display_link($scorm_item->get_url(true));
			
		return implode("\n", $html);
	}
}

?>