<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_publisher.class.php';

class ToolIntroductionPublisherComponent extends ToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		$pub = new LearningObjectPublisher($this, 'introduction', true);
		$pub->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_PUBLISH_INTRODUCTION);
		$html[] = '<p><a href="' . $this->get_url() . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();
		
		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>