<?php

require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';
require_once dirname(__FILE__) . '/document_viewer/document_browser.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object/document/document.class.php';

class DocumentToolViewerComponent extends DocumentToolComponent
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

		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'document');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_action_bar();

		$browser = new DocumentBrowser($this);
		$trail = new BreadcrumbTrail();

        if(Request :: get('tool_action') == null && Request :: get('pid') == null)
        {
            $breadcrumbs = $browser->get_publication_category_tree()->get_breadcrumbs();
            unset($breadcrumbs[0]);
            foreach($breadcrumbs as $breadcrumb)
            {
                $trail->add(new BreadCrumb($breadcrumb['url'], $breadcrumb['title']));
            }
        }
        elseif(Request :: get('pcattree') > 0)
        {
            foreach(Tool ::get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
                $trail->add(new BreadCrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }
        //dump($browser->get_publication_category_tree()->get_breadcrumbs());
        //dump(Tool ::get_pcattree_parents(Request :: get('pcattree')));
        if(Request :: get('pid') != null)
        {
        	$trail->add(new BreadCrumb($this->get_url(array(Tool :: PARAM_ACTION => DocumentTool ::ACTION_VIEW_DOCUMENTS, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_learning_object_publication(Request :: get('pid'))->get_learning_object()->get_title()));
        }
        $trail->add_help('courses document tool');
		$this->display_header($trail, true);

		if(!Request :: get('pid'))
		{
			if(PlatformSetting :: get('enable_introduction', 'weblcms'))
			{
				echo $this->display_introduction_text($this->introduction_text);
			}
		}
        $html = $browser->as_html();
		echo $this->action_bar->as_html();
		echo '<div id="action_bar_browser">';
		echo $html;
		echo '</div>';

		$this->display_footer();
	}

	function add_actionbar_item($item)
	{
		$this->action_bar->add_tool_action($item);
	}

	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$cat_id = Request :: get('pcattree');
		$category = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_category($cat_id);
		
		if(!Request :: get('pid'))
		{
			$action_bar->set_search_url($this->get_url());
			if($this->is_allowed(ADD_RIGHT) || ($category && $category->get_name() == Translation :: get('Dropbox')))
			{
				$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			}
		}

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!Request :: get('pid') && $this->is_allowed(EDIT_RIGHT))
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

		if(!Request :: get('pid'))
		{
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path().'action_save.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_ZIP_AND_DOWNLOAD)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get('Slideshow'), Theme :: get_common_image_path().'action_slideshow.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_SLIDESHOW)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}

		if($this->is_allowed(EDIT_RIGHT))
		{
        	$action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
		}

		return $action_bar;
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

	/*function display_introduction_text()
	{
		$html = array();

		$introduction_text = $this->introduction_text;

		if($introduction_text)
		{

			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path() . 'action_edit.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);

			$tb_data[] = array(
				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $introduction_text->get_id())),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);

			$html[] = '<div class="learning_object">';
			$html[] = '<div class="description">';
			$html[] = $introduction_text->get_learning_object()->get_description();
			$html[] = '</div>';
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}

		return implode("\n",$html);
	}*/
}
?>