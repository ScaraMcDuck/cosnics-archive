<?php

require_once Path :: get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/learning_path_publication_table/learning_path_publication_table.class.php';

class LearningPathToolViewerComponent extends LearningPathToolComponent
{
	private $action_bar;
	
	function run()
	{
		if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

		$trail = new BreadcrumbTrail();
		$this->display_header($trail);
		$this->action_bar = $this->get_toolbar();
		echo $this->action_bar->as_html();
		$table = new LearningPathPublicationTable($this, $this->get_user(), array('learning_path'), null);
		echo $table->as_html();
		
		$this->display_footer();
	}
}
?>