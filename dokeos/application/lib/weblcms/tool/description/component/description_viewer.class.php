<?php

require_once dirname(__FILE__) . '/../description_tool.class.php';
require_once dirname(__FILE__) . '/../description_tool_component.class.php';
require_once dirname(__FILE__) . '/description_viewer/description_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class DescriptionToolViewerComponent extends DescriptionToolComponent
{
	private $bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		$this->bar = new ActionBarRenderer(array(), $this->get_left_actions(), $this->get_url());
		$browser = new DescriptionBrowser($this);
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		
		echo '<br /><a name="top"></a>';
		//echo $this->perform_requested_actions();
		echo $this->bar->as_html() . '<br />';
		echo '<div style="width:100%; float:right;">';
		echo $browser->as_html();
		echo '</div>';
		
		$this->display_footer();
	}
	
	function get_left_actions()
	{
		$actions = array();
		
		$actions[] = array(
				'href' => $this->get_url(array(DescriptionTool :: PARAM_ACTION => DescriptionTool :: ACTION_PUBLISH)),
				'label' => Translation :: get('Publish'),
				'img' => Theme :: get_common_img_path().'action_publish.png'
		);
		
		$actions[] = array(
				'href' => $this->get_url(),
				'label' => Translation :: get('Show All'),
				'img' => Theme :: get_common_img_path().'action_browser.png'
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