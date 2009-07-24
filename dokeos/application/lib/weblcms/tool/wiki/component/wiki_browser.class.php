<?php

/*
 * This is the first page you'll get when adding a wiki to a course.
 * It shows a list of every available wiki. You can edit, delete or hide a wiki.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_library_path().'/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path().'/lib/complex_display/complex_display.class.php';
require_once Path :: get_application_path().'/lib/weblcms/tool/wiki/component/wiki_publication_table/wiki_publication_table.class.php';

class WikiToolBrowserComponent extends WikiToolComponent
{
	private $action_bar;
    private $introduction_text;

	function run()
	{
		if (!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		
		$conditions = array();
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
		$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'wiki');
		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		$condition = new AndCondition($conditions);
		
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications_new($condition);
		$this->introduction_text = $publications->next_result();

		$this->action_bar = $this->get_toolbar();

		$trail = new BreadcrumbTrail();
		$trail->add_help('courses wiki tool');

		$this->display_header($trail, true);
		if(PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			echo $this->display_introduction_text();
		}

		echo $this->action_bar->as_html();
		$table = new WikiPublicationTable($this, $this->get_user(), array('wiki'), null);
		echo $table->as_html();

		$this->display_footer();
	}


//    function run()
//	{
//		if(!$this->is_allowed(VIEW_RIGHT))
//		{
//			Display :: not_allowed();
//			return;
//		}
//
//        $this->action_bar = $this->get_toolbar();
//
//        $cd = ComplexDisplay :: factory($this);
//        $cd->run();
//
//        switch($cd->get_action())
//        {
//            case WikiDisplay ::ACTION_BROWSE_WIKIS:
//                Events :: trigger_event('browse', 'weblcms', array('course' => Request :: get('course')));
//                break;
//        }
//    }
//
//	function get_url($parameters = array (), $filter = array(), $encode_entities = false)
//	{
//        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
//		return $this->get_parent()->get_url($parameters, $filter, $encode_entities);
//	}
//
//    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
//	{
//        //$parameters[Tool :: PARAM_ACTION] = GlossaryTool :: ACTION_BROWSE_GLOSSARIES;
//		$this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
//	}



	function get_toolbar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->set_search_url($this->get_url());
            $action_bar->add_common_action(
                new ToolbarItem(
                    Translation :: get('CreateWiki'), Theme :: get_common_image_path().'action_create.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
                )
            );


		/*$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/

		if(!$this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms'))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path().'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		}
		return $action_bar;
	}

	function display_introduction_text()
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
	}
}
?>