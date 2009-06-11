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

	function run()
	{
        $dm = RepositoryDataManager :: get_instance();    

        $this->action_bar = WikiDisplay :: get_toolbar($this,Request :: get('pid'),$this->get_root_lo(), null, $this->get_parent()->get_parent()->get_course()->get_id());
        echo '<div id="trailbox2" style="padding:0px;">'.$this->get_parent()->get_breadcrumbtrail()->render().'<br /><br /><br /></div>';
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        
        if($this->get_root_lo() != null)
        {
            echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.$this->get_root_lo()->get_title().'</div><hr style="height:1px;color:#4271B5;width:100%;">';
            $table = new WikiPageTable($this, $this->get_root_lo()->get_id());
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
