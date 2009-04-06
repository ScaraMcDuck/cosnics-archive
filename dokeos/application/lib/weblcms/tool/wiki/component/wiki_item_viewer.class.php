<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolItemViewerComponent extends WikiToolComponent
{
	private $action_bar;
	private $introduction_text;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $publication_id = Request :: get('pid');
        $dm = RepositoryDataManager :: get_instance();
        $publication = $dm->retrieve_learning_object($publication_id);
        
		$this->display_header(new BreadcrumbTrail());
        echo '<h2>'.$publication->get_title().'</h2>';
        echo $publication->get_description();
        $this->display_footer();
	}
}
?>