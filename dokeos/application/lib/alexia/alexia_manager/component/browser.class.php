<?php
/**
 * @package alexia
 * @subpackage alexia_manager
 * @subpackage component
 * 
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__).'/../alexia_manager.class.php';
require_once dirname(__FILE__).'/../alexia_manager_component.class.php';
require_once dirname(__FILE__) . '/alexia_publication_browser/alexia_publication_browser_table.class.php';
require_once Path :: get_library_path() . 'html/action_bar/action_bar_renderer.class.php';

class AlexiaManagerBrowserComponent extends AlexiaManagerComponent
{
	private $introduction;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Library')));
		$trail->add_help('alexia general');
		
		$this->get_introduction();
		
		$this->display_header($trail);
		echo '<a name="top"></a>';
		echo $this->get_introduction_html();
		echo $this->get_action_bar_html() . '';
		echo '<div id="action_bar_browser">';
		echo $this->get_publications_html();
		echo '</div>';
		$this->display_footer();
	}
	
    private function get_publications_html()
    {
        $parameters = $this->get_parameters(true);
        
        $table = new AlexiaPublicationBrowserTable($this, null, $parameters, $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }
    
    function get_condition()
    {
		$subselect_condition = new EqualityCondition(LearningObject :: PROPERTY_TYPE, 'link');
		$condition = new SubselectCondition(AlexiaPublication :: PROPERTY_LEARNING_OBJECT, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
    	
    	return $condition;
    }
    
    function get_introduction()
    {		
		$subselect_condition = new EqualityCondition('type', 'introduction');
		$condition = new SubselectCondition(AlexiaPublication :: PROPERTY_LEARNING_OBJECT, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
		
		$publications = AlexiaDataManager :: get_instance()->retrieve_alexia_publications($condition);
		if (!$publications->is_empty())
		{
			$this->introduction = $publications->next_result();
		}
    }

	function get_action_bar_html()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_CREATE_PUBLICATION))));
		if (!isset($this->introduction))
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('AddIntroduction'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_PUBLISH_INTRODUCTION))));
		}
		$action_bar->set_search_url($this->get_url());
//		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ListView'), Theme :: get_image_path().'tool_calendar_down.png', $this->get_url(array (Application :: PARAM_ACTION => PersonalCalendarManager :: ACTION_BROWSE_CALENDAR, 'view' => 'list'))));
		return $action_bar->as_html();
	}
	
	function get_introduction_html()
	{
		$introduction = $this->introduction;
		$html = array();

		if(isset($introduction))
		{

//			$tb_data[] = array(
//				'href' => $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $introduction->get_id())),
//				'label' => Translation :: get('Edit'),
//				'img' => Theme :: get_common_image_path() . 'action_edit.png',
//				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
//			);
//
			$tb_data[] = array(
				'href' => $this->get_publication_deleting_url($introduction),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path() . 'action_delete.png',
				'display' => DokeosUtilities :: TOOLBAR_DISPLAY_ICON
			);

			$object = $introduction->get_publication_object();

			$html[] = '<div class="introduction" style="background-image: url(' . Theme :: get_common_image_path() . 'learning_object/introduction.png);">';
			$html[] = '<div class="title">';
			$html[] = $object->get_title();
			$html[] = '</div>';
			$html[] = '<div class="clear">&nbsp;</div>';
			$html[] = '<div class="description">';
			$html[] = $object->get_description();
			$html[] = '</div>';
			$html[] = DokeosUtilities :: build_toolbar($tb_data) . '<div class="clear"></div>';
			$html[] = '</div>';
			$html[] = '<br />';
		}

		return implode("\n", $html);
	}
}
?>