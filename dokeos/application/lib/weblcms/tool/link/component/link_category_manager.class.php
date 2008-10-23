<?php

require_once dirname(__FILE__) . '/../link_tool.class.php';
require_once dirname(__FILE__) . '/../link_tool_component.class.php';
require_once dirname(__FILE__).'/../../../learning_object_publication_category_manager.class.php';

class LinkToolCategoryManagerComponent extends LinkToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$catman = new LearningObjectPublicationCategoryManager($this, 'link');
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		echo $catman->as_html();
		$this->display_footer();
	}
}
?>