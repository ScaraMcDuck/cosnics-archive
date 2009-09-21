<?php
/**
 * $Id: browser.class.php 22148 2009-07-16 12:54:53Z vanpouckesven $
 * @package repository.repositorymanager
 *
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__) . '/browser/template_browser/template_browser_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class RepositoryManagerTemplateBrowserComponent extends RepositoryManagerComponent
{
    private $action_bar;
    
	/**
     * Runs this component and displays its output.
     */
    function run()
    {
    	$this->action_bar = $this->get_action_bar();
    
    	$trail = new BreadcrumbTrail(false);
    	$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseTemplates')));
    	
    	$this->display_header($trail);
    	echo $this->action_bar->as_html();
    	echo $this->get_table_html();
    	$this->display_footer();
    }
    
    function get_table_html()
    {
    	$condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $table = new TemplateBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }
    
    function get_condition()
    {
    	$conditions = array();
    	$conditions[] = new EqualityCondition(LearningObject :: PROPERTY_OWNER_ID, 0);
    	
     	$query = $this->action_bar->get_query();
        if(isset($query) && $query != '')
        {
            $or_conditions[] = new LikeCondition(LearningObject :: PROPERTY_TITLE, $query);
            $or_conditions[] = new LikeCondition(LearningObject :: PROPERTY_DESCRIPTION, $query);

            $conditions[] = new OrCondition($or_conditions);
        }
        
        return new AndCondition($conditions);
    	
    }
    
	function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
       
        return $action_bar;
    }

}
?>
