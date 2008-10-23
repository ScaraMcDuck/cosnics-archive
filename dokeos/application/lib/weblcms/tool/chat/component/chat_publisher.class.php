<?php

require_once dirname(__FILE__) . '/../chat_tool.class.php';
require_once dirname(__FILE__) . '/../chat_tool_component.class.php';
require_once dirname(__FILE__).'/../../../learning_object_publisher.class.php';

class ChatToolPublisherComponent extends ChatToolComponent
{
	function run()
	{
		if(!$this->is_allowed(ADD_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		$pub = new LearningObjectPublisher($this, 'chatbox', true);
		
		$html[] = '<p><a href="' . $this->get_url(array(), true) . '"><img src="'.Theme :: get_common_img_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
		$html[] =  $pub->as_html();
		
		$this->display_header($trail);
		echo implode("\n",$html);
		$this->display_footer();
	}
}
?>