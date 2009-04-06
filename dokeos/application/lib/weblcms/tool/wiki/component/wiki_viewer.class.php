<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiToolViewerComponent extends WikiToolComponent
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
        $wm = WeblcmsDataManager :: get_instance();
        $dm = RepositoryDataManager :: get_instance();
        $publication = $wm->retrieve_learning_object_publication($publication_id);
        $object_id = $publication->get_learning_object()->get_id();
        
        $wiki = $dm->retrieve_learning_object($object_id);      
		$this->display_header(new BreadcrumbTrail());
        echo '<h2>Title : ' .$wiki->get_default_property('title') .'</h2>';
        
        /*
         *  check which pages are linked to this wiki, loop them and show the name (basic)
         */
        $table = new WikiPageTable($this, $wiki->get_id());
		echo $table->as_html();
        
        $this->display_footer();
	}
}
?>