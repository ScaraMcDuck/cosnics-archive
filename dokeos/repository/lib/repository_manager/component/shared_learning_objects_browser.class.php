<?php
/**
 * $Id: browser.class.php 22148 2009-07-16 12:54:53Z vanpouckesven $
 * @package repository.repositorymanager
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/browser/repository_browser_table.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__).'/../../forms/repository_filter_form.class.php';
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerSharedLearningObjectsBrowserComponent extends RepositoryManagerComponent
{
    private $form;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add_help('repository general');

        $this->action_bar = $this->get_action_bar();
        $this->form = new RepositoryFilterForm($this, $this->get_url());
        $output = $this->get_learning_objects_html();

        $query = $this->action_bar->get_query();
        if(isset($query) && $query != '')
        {
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Search')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SearchResultsFor').': '.$query));
        }
		
        $session_filter = Session :: retrieve('filter');
        
        if($session_filter != null && !$session_filter == 0)
        {
            if(is_numeric($session_filter))
            {
                $condition = new EqualityCondition(UserView :: PROPERTY_ID, $session_filter);
                $user_view = RepositoryDataManager::get_instance()->retrieve_user_views($condition)->next_result();
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . $user_view->get_name()));
            }
            else
                $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Filter') . ': ' . DokeosUtilities :: underscores_to_camelcase(($session_filter))));
        }


        $this->display_header($trail, false, true);

        echo $this->action_bar->as_html();
        echo '<br />' . $this->form->display() . '<br />';
        echo $output;
        echo ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');

        $this->display_footer();
    }
    /**
     * Gets the  table which shows the learning objects in the currently active
     * category
     */
    private function get_learning_objects_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $types = Request :: get(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE] = $types;
        }
        $table = new RepositoryBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }

    private function get_condition()
    {
        //TODO: limit this so only the shared objects are seen (view and use)
    	$query = $this->action_bar->get_query();
        if(isset($query) && $query != '')
        {
            $or_conditions[] = new LikeCondition(LearningObject :: PROPERTY_TITLE, $query);
            $or_conditions[] = new LikeCondition(LearningObject :: PROPERTY_DESCRIPTION, $query);

            $conditions[] = new OrCondition($or_conditions);
        }

        $cond = $this->form->get_filter_conditions();
        if($cond)
        {
            $conditions[] = $cond;
        }

        if($conditions)
        	$condition = new AndCondition($conditions);
       
        if(!$condition)
        {
        	$condition = new EqualityCondition('id', -1);
        }
   
        return $condition;
    }

}
?>
