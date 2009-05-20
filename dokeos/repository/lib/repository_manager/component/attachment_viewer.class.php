<?php

require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class RepositoryManagerAttachmentViewerComponent extends RepositoryManagerComponent
{
	
	function run()
	{
		/*if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/
		
		$object_id = Request :: get('object');
		if($object_id)
		{
			$trail = new BreadCrumbTrail();
			$trail->add(new BreadCrumb($this->get_url(array('object' => $object_id)), Translation :: get('ViewAttachment')));
			$this->display_header($trail, false, false, 'repository general');
			
			echo '<a href="javascript:history.go(-1)">' . Translation :: get('Back') . '</a><br /><br />';
			
			$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object_id);
			$display = LearningObjectDisplay :: factory($object);
			
			echo $display->get_full_html();
			
			$this->display_footer();
			
		}
		else
		{
			$this->display_header(new BreadCrumbTrail(), false, true, 'repository general');
			$this->display_error_message('NoObjectSelected');
			$this->display_footer();
		}

	}
}
?>