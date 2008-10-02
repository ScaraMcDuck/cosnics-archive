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
		echo $this->perform_requested_actions();
		
		$browser = new AnnouncementBrowser($this);
		$publications = $browser->get_publications();
		
		echo '<div style="width:19%; float: left;">';
		echo '<div style="border-bottom: 1px solid grey; padding: 5px; line-height: 25px;">';
		
		if($this->is_allowed(ADD_RIGHT))
		{
			echo '<a href="' . $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH), true) . '"><img src="'.Theme :: get_common_img_path().'action_publish.png" alt="'.Translation :: get('Publish').'" style="vertical-align:middle;"/> '.Translation :: get('Publish').'</a>';
		}
		
		$index = 0;
		$publication_ids = array();
		foreach($publications as $publication)
		{
			$publication_html[] = '<a href="#' . $index . '">' . $browser->get_publication_list_renderer()->render_title($publication) . '</a><br />';
			$publication_ids[] = $publication->get_id();
			$index++;
		}
	
		if(count($publications) >= 1)
		{
			$delete_url = $this->get_url(array (RepositoryTool :: PARAM_ACTION => RepositoryTool :: ACTION_DELETE_SELECTED, RepositoryTool :: PARAM_PUBLICATION_ID => $publication_ids), true);
			echo '<br /><a href="'.$delete_url.'" onclick="return confirm(\''.addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))).'\');"><img src="'.Theme :: get_common_img_path().'action_delete.png"  alt="' . Translation :: get('Clear_list_of_announcements') . '"/> ' . Translation :: get('Clear_list_of_announcements') . '</a>';
		}
		
		echo '</div><div style="padding: 5px; line-height: 20px;">';
		
		echo implode("\n", $publication_html);
		
		echo '</div></div><div style="width:79%; padding-left: 1%; float:right; border-left: 1px solid grey;">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
}
?>