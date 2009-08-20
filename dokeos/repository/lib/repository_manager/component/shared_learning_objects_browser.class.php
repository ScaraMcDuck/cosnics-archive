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
require_once dirname(__FILE__).'/browser/shared_learning_objects_browser/repository_shared_learning_objects_browser_table.class.php';
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
        $table = new RepositorySharedLearningObjectsBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        return $action_bar;
    }

    function has_right($learning_object_id,$right)
    {
        foreach ($this->list as $key => $value)
        {
            if($value['learning_object'] == $learning_object_id && $value['right'] == $right)
                return true;
        }
        return false;
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

        $rdm = RightsDataManager :: get_instance();
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();

        //retrieve the roles for this user and its groups
        $role_cond = new EqualityCondition(RightsTemplate :: PROPERTY_USER_ID, $this->get_user_id());
        $user_roles = $udm->retrieve_user_rights_templates($role_cond);

        while($user_role = $user_roles->next_result())
        {
            if(!in_array($user_role->get_rights_template_id(), $roles))
                $roles[] = $user_role->get_rights_template_id();
        }

        $groups = $gdm->retrieve_user_groups($this->get_user_id());

        while($group = $groups->next_result())
        {
            $group_roles_cond = new EqualityCondition(GroupRightsTemplate :: PROPERTY_GROUP_ID, $group->get_group_id());
            $group_roles = $gdm->retrieve_group_rights_templates($group_roles_cond);
            while($group_role = $group_roles->next_result())
            {
                if(!in_array($group_role->get_rights_template_id(), $roles))
                    $roles[] = $group_role->get_rights_template_id();
            }
        }

        //retrieve all the rights
        $reflect = new ReflectionClass(Application :: application_to_class(RepositoryManager :: APPLICATION_NAME) . 'Rights');
        $rights_db = $reflect->getConstants();

        foreach($rights_db as $right_name => $right_id)
        {
            if($right_id != RepositoryRights :: VIEW_RIGHT && $right_id != RepositoryRights :: USE_RIGHT && $right_id != RepositoryRights :: REUSE_RIGHT)
                continue;
            $rights[] = $right_id;
        }


        $shared_learning_objects = $rdm->retrieve_shared_learning_objects($roles,$rights);

        while($role_right_location = $shared_learning_objects->next_result())
        {
            if(!in_array($role_right_location->get_location_id(), $location_ids))
                $location_ids[] = $role_right_location->get_location_id();

            $this->list[] = array('location_id' => $role_right_location->get_location_id(),'role' => $role_right_location->get_role_id(), 'right' => $role_right_location->get_right_id());
        }

        $location_cond = new InCondition('id',$location_ids);
        $locations = $rdm->retrieve_locations($location_cond);

        while($location = $locations->next_result())
        {
            $ids[] = $location->get_identifier();

            foreach ($this->list as $key => $value)
            {
                if($value['location_id'] == $location->get_id())
                {
                    $value['learning_object'] = $location->get_identifier();
                    $this->list[$key] = $value;
                }
            }
        }

        if($ids)
            $conditions[] = new InCondition('id',$ids);


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
