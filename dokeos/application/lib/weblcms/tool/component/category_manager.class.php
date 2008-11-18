<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once dirname(__FILE__).'/../../category_manager/learning_object_publication_category_manager.class.php';

class ToolCategoryManagerComponent extends ToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: display_not_allowed();
			return;
		}
		
		$trail = new BreadcrumbTrail();
	//	$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ManageCategories')));
		
		$category_manager = new LearningObjectPublicationCategoryManager($this);
		$category_manager->set_parameter(Tool :: PARAM_ACTION, Tool :: ACTION_MANAGE_CATEGORIES);
		ob_start();
		$this->display_header($trail);
		$category_manager->run();
		$this->display_footer();
		ob_end_flush();
	}
}
?>