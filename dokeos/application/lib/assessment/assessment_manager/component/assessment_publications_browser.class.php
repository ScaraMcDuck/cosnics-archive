<?php
/**
 * @package application.assessment.assessment.component
 */

require_once dirname(__FILE__).'/../assessment_manager.class.php';
require_once dirname(__FILE__).'/../assessment_manager_component.class.php';
require_once dirname(__FILE__).'/../../assessment_publication_category_menu.class.php';
require_once dirname(__FILE__).'/assessment_publication_browser/assessment_publication_browser_table.class.php';

/**
 * assessment component which allows the user to browse his assessment_publications
 * @author Sven Vanpoucke
 * @author 
 */
class AssessmentManagerAssessmentPublicationsBrowserComponent extends AssessmentManagerComponent
{
	private $action_bar;
	
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseAssessmentPublications')));

		$this->action_bar = $this->get_action_bar();
		$menu = $this->get_menu();
		$trail->merge($menu->get_breadcrumbs());
		$this->display_header($trail);

		echo $this->action_bar->as_html();
		echo '<div id="action_bar_browser">';
		echo '<div style="float: left; width: 17%; overflow: auto;">';
		echo $menu->render_as_tree();
		echo '</div>';
		echo '<div style="width: 80%; float: right;">';
		echo $this->get_table();
		echo '</div>';
		echo '</div>';
		$this->display_footer();
	}

	function get_table()
	{
		$table = new AssessmentPublicationBrowserTable($this, array(Application :: PARAM_APPLICATION => 'assessment', Application :: PARAM_ACTION => AssessmentManager :: ACTION_BROWSE_ASSESSMENT_PUBLICATIONS), $this->get_condition());
		return $table->as_html();
	}
	
	function get_menu()
	{
		$current_category = Request :: get('category');
		$current_category = $current_category ? $current_category : 0;
		$menu = new AssessmentPublicationCategoryMenu($current_category);
		return $menu;
	}
	
	function get_action_bar()
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->set_search_url($this->get_url());
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $this->get_create_assessment_publication_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path().'action_category.png', $this->get_manage_assessment_publication_categories_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ViewResultsSummary'), Theme :: get_common_image_path().'action_view_results.png', $this->get_assessment_results_viewer_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportQTI'), Theme :: get_common_image_path().'action_import.png', $this->get_import_qti_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		return $action_bar;
	}
	
	function get_condition()
	{
		$current_category = Request :: get('category');
		$current_category = $current_category ? $current_category : 0;
		
		return new EqualityCondition(AssessmentPublication :: PROPERTY_CATEGORY, $current_category);
	}

}
?>