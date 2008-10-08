<?php

require_once dirname(__FILE__) . '/../announcement_tool.class.php';
require_once dirname(__FILE__) . '/../announcement_tool_component.class.php';
require_once dirname(__FILE__) . '/announcement_viewer/announcement_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class AnnouncementToolViewerComponent extends AnnouncementToolComponent
{
	private $bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$this->bar = new ActionBarRenderer($this->get_left_actions(), array(), $this->get_url());
		$browser = new AnnouncementBrowser($this);
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		echo $this->perform_requested_actions();
		echo $this->bar->as_html();
		
		/*$publications = $browser->get_publications();
	
		//echo '<div style="width:19%; float: left;">';
		
		$index = 0;
		$publication_ids = array();
		foreach($publications as $publication)
		{
			$publication_html[] = '<a href="#' . $index . '">' . $browser->get_publication_list_renderer()->render_title($publication) . '</a><br />';
			$publication_ids[] = $publication->get_id();
			$index++;
		}
		
		//echo '<div style="padding: 5px; line-height: 20px;">';
		//echo implode("\n", $publication_html);
		
		//echo '</div></div>';*/

		echo '<div style="width:100%; float:right;>';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function get_left_actions()
	{
		$actions = array();
		
		$actions[] = array(
				'href' => $this->get_url(array(AnnouncementTool :: PARAM_ACTION => AnnouncementTool :: ACTION_PUBLISH)),
				'label' => Translation :: get('Publish'),
				'img' => Theme :: get_common_img_path().'action_publish.png'
		);
		
		return $actions;
	}
	
	function get_condition()
	{
		$query = $this->bar->get_query();
		if(isset($query) && $query != '')
		{
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_TITLE, $query);
			$conditions[] = new LikeCondition(LearningObject :: PROPERTY_DESCRIPTION, $query);
			return new OrCondition($conditions);
		}
		
		return null;
	}
}
?>