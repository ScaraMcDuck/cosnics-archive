<?php

/*
 * This viewer will show the selected wiki_page.
 * You'll be redirected here from the wiki_viewer page by clicking on the name of a wiki_page
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

//require_once dirname(__FILE__) . '/../wiki_tool.class.php';
//require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once dirname(__FILE__).'/wiki_parser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class WikiDisplayWikiItemViewerComponent extends WikiDisplayComponent
{
	private $action_bar;
    private $wiki_id;
    private $cid;
    private $wiki_page;
    private $links;


	function run()
	{
        /*
         * publication and complex object id are requested.
         * These are used to retrieve
         *  1) the complex object ( reference is stored )
         *  2) the learning object ( actual inforamation about a wiki_page is stored here )
         *
         */
        $this->wiki_id = Request :: get('pid');
        $this->cid = Request :: get('cid');
        $dm = RepositoryDataManager :: get_instance();
        $this->links = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->wiki_id)->get_links();

       /*
        *  If a complex object id is passed, the object will be retrieved
        */
        if(!empty($this->cid))
        {
            $cloi = $dm->retrieve_complex_learning_object_item($this->cid);
            $this->wiki_page = $dm->retrieve_learning_object($cloi->get_ref());
        }

        $trail = new BreadcrumbTrail();
        $trail->add_help('courses wiki tool');
        $trail->add(new BreadCrumb($this->get_url(array(WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id)), $dm->retrieve_learning_object($this->wiki_id)->get_title()));
        $trail->add(new BreadCrumb($this->get_url(array(WikiDisplay ::PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, Tool :: PARAM_PUBLICATION_ID => $this->wiki_id, Tool :: PARAM_COMPLEX_ID => $this->cid)), DokeosUtilities::truncate_string($this->wiki_page->get_title(),20)));
        

        $this->get_parent()->get_parent()->display_header($trail, true);

        $this->action_bar = WikiDisplay :: get_toolbar($this,$this->wiki_id, $this->cid, $this->get_parent()->get_parent()->get_course()->get_id());//$this->get_toolbar();
        echo  '<div style="float:left; width: 135px;">'.$this->action_bar->as_html().'</div>';
        echo  '<div style="padding-left: 15px; margin-left: 150px; border-left: 1px solid grey;"><div style="font-size:20px;">'.$this->wiki_page->get_title().'</div><hr style="height:1px;color:#4271B5;width:100%;">';

        /*
         *  Here we create the wiki_parser component.
         *  For more information about the parser, please read the information provided in the wiki_parser class
         */
		$parser = new WikiToolParserComponent($this->wiki_id,$this->get_parent()->get_parent()->get_course_id(),$this->wiki_page->get_description(),$this->cid);
        echo $parser->parse_wiki_text();
        echo $parser->get_wiki_text();
        /*
         * If you don't want the bottom link to show, put the next line in comment
         */
        echo '<div ><a href=#top>'.'back to top'.'</a></div>';
        echo '</div>';
	}
}
?>