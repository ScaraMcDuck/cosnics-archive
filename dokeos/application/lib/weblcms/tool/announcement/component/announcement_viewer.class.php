<?php

require_once dirname(__FILE__) . '/../announcement_tool.class.php';
require_once dirname(__FILE__) . '/../announcement_tool_component.class.php';
require_once dirname(__FILE__) . '/announcement_viewer/announcement_browser.class.php';

class AnnouncementToolViewerComponent extends AnnouncementToolComponent
{
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		echo '<a name="top"></a>';
		if($this->is_allowed(ADD_RIGHT))
		{
			echo '<p><a href="' . $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH), true) . '"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a></p>';
		}
		echo $this->perform_requested_actions();
		$browser = new AnnouncementBrowser($this);
		echo $browser->as_html();
		$this->display_footer();
	}
}
?>