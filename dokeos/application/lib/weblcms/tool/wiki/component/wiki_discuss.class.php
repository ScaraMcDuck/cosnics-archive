<?php

require_once dirname(__FILE__) . '/../wiki_tool.class.php';
require_once dirname(__FILE__) . '/../wiki_tool_component.class.php';
require_once dirname(__FILE__).'/wiki_page_table/wiki_page_table.class.php';
require_once Path :: get_repository_path().'lib/learning_object_display.class.php';
require_once Path :: get_repository_path().'lib/learning_object_pub_feedback.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/wiki_parser.class.php';

class WikiToolDiscussComponent extends WikiToolComponent
{
	private $action_bar;
    private $wiki_page_id;
    private $wiki_id;
    private $cid;
    private $fid;
    private $publication_id;


	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}

        $dm = RepositoryDataManager :: get_instance();
        $rm = new RepositoryManager();
        $this->publication_id = Request :: get('pid');
        $this->cid = Request :: get('cid');
        
        $complexeObject = $dm->retrieve_complex_learning_object_item($this->cid);
        if(isset($complexeObject))
        {
            $this->wiki_page_id = $complexeObject->get_ref();
            $this->wiki_id = $complexeObject->get_parent();
        } 
        $wiki_page = $dm->retrieve_learning_object($this->wiki_page_id);
        
		$this->display_header(new BreadcrumbTrail());

        $this->action_bar = $this->get_toolbar();
        echo '<br />' . $this->action_bar->as_html();
        
        echo '<h2>' .Translation :: get('DiscussThe') .$wiki_page->get_title().' ' . Translation :: get('Page') .'</h2>';
        $display = LearningObjectDisplay :: factory($wiki_page);
        $parser = new WikiToolParserComponent();
        $parser->set_pid(Request :: get('pid'));
        $parser->set_course_id($this->get_course_id());
        echo $parser->handle_internal_links($display->get_full_html());
        
        if(isset($this->cid)&& isset($this->publication_id))
        {
            $conditions[] = new EqualityCondition(LearningObjectPubFeedback :: PROPERTY_PUBLICATION_ID, $this->publication_id);
            $conditions[] = new EqualityCondition(LearningObjectPubFeedback :: PROPERTY_CLOI_ID, $this->cid);
            $condition = new AndCondition($conditions);
            $feedbacks = $dm->retrieve_learning_object_pub_feedback($condition);
            while($feedback = $feedbacks->next_result())
            {
                if($i == 0)
                {
                    echo '<h3>' . Translation :: get('Feedback') . '</h3>';
                }
                $this->fid = $feedback->get_feedback_id();
                $feedback_display = $dm->retrieve_learning_object($this->fid);
                $creationDate = $feedback_display->get_creation_date();
                echo date("F j, Y, H:i:s",$creationDate );
                $feedbackdisplay = LearningObjectDisplay :: factory($feedback_display);                
                echo $feedbackdisplay->get_full_html();
                echo $this->build_actions() . '<br /><br />';
                $i++;

            }
        }
        
        $this->display_footer();
    }


    function build_actions()
    {
        $actions[] = array(
			'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE_FEEDBACK, 'fid' => $this->fid, 'cid' => $this->cid, 'pid' => $this->publication_id)),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png'
			);

        $actions[] = array(
			'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_FEEDBACK, 'fid' => $this->fid, 'cid' => $this->cid, 'pid' => $this->publication_id)),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
			);
        
        return DokeosUtilities :: build_toolbar($actions);

    }


    function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());


        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('CreateWikiPage'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(Tool :: PARAM_ACTION => WikiTool :: ACTION_CREATE_PAGE, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('BrowseWiki'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool ::ACTION_VIEW_WIKI, 'pid' => $this->publication_id)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool:: ACTION_DELETE_CLOI, 'pid' => $this->publication_id,'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('AddFeedback'), Theme :: get_common_image_path().'action_add.png', $this->get_url(array(WikiTool :: PARAM_ACTION => Tool :: ACTION_FEEDBACK_CLOI, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('History'), Theme :: get_common_image_path().'action_versions.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);

        /*$action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('NotifyChanges'), Theme :: get_common_image_path().'action_subscribe.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_HISTORY, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/

        $action_bar->add_tool_action(
			new ToolbarItem(
				Translation :: get('Statistics'), Theme :: get_common_image_path().'action_reporting.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_PAGE_STATISTICS, 'pid' => $this->publication_id, 'cid' => $this->cid)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);


		return $action_bar;
	}
}

?>
