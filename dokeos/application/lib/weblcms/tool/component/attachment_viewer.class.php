<?php

require_once dirname(__FILE__) . '/../tool.class.php';
require_once dirname(__FILE__) . '/../tool_component.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class ToolAttachmentViewerComponent extends ToolComponent
{
	private $action_bar;
	
	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$object_id = Request :: get('object_id');
		if($object_id)
		{
			$trail = new BreadCrumbTrail();
			$trail->add(new BreadCrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
			$this->display_header($trail, true, 'courses general');
			
			echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
			
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object_id);
			$display = LearningObjectDisplay :: factory($object);
			
			echo $display->get_full_html();
			
			$this->display_footer();
			
		}
		else
		{
			$this->display_header($trail, true, 'courses general');
			$this->display_error_message('NoObjectSelected');
			$this->display_footer();
		}

	}
}
?>