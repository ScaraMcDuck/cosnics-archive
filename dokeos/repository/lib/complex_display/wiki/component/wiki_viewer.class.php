<?php

/*
 * This is the compenent that allows the user to view all pages of a wiki.
 * If no homepage is set all available pages will be shown, otherwise the homepage will be shown.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/wiki_parser.class.php';
require_once Path :: get_repository_path().'lib/complex_display/wiki/wiki_display.class.php';

class WikiDisplayWikiViewerComponent extends WikiDisplayComponent
{
	private $action_bar;
    private $links;
    private $current_wiki;

	function run()
	{

        $dm = RepositoryDataManager :: get_instance();    

        $this->current_wiki = $dm->retrieve_learning_object(Request :: get('pid'));

        $trail = new BreadcrumbTrail();
        $trail->add(new BreadCrumb($this->get_url(array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->current_wiki->get_id())), DokeosUtilities::truncate_string($this->current_wiki->get_title(),20)));
        $trail->add_help('courses wiki tool');
        $this->get_parent()->get_parent()->display_header($trail, true);

        $this->links = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->current_wiki->get_id())->get_links();

        $this->action_bar = WikiDisplay :: get_toolbar($this,$this->current_wiki->get_id(), null, $this->get_parent()->get_parent()->get_course()->get_id());//$this->get_toolbar($this->current_wiki);
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        if(!empty($this->current_wiki))
        {
            echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.$this->current_wiki->get_title().'</div><hr style="height:1px;color:#4271B5;width:100%;">';
            $table = new WikiPageTable($this, $this->current_wiki->get_id());
            echo $table->as_html().'</div>';
        }
	}

    function get_condition()
	{
		$query = $this->action_bar->get_query();
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
