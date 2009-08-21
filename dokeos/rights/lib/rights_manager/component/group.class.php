<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once Path :: get_rights_path() . 'lib/rights_manager/rights_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_manager/rights_manager_component.class.php';
require_once Path :: get_rights_path() . 'lib/rights_data_manager.class.php';
require_once Path :: get_rights_path() . 'lib/rights_utilities.class.php';
require_once Path :: get_rights_path() . 'lib/group_right_manager/group_right_manager.class.php';
/**
 * Admin component
 */
class RightsManagerGroupComponent extends RightsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_MANAGE_GROUP_RIGHTS)), Translation :: get('ManageGroupRights')));
		$trail->add_help('rights general');

//        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
//        {
//            $this->display_header($trail);
//            $this->display_error_message(Translation :: get('NotAllowed'));
//            $this->display_footer();
//            exit();
//        }

        $package_manager = new GroupRightManager($this->get_parent());
        $package_manager->run();
    }
}
?>