<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../rights_manager.class.php';
require_once dirname(__FILE__).'/../rights_manager_component.class.php';
require_once dirname(__FILE__).'/../../rights_data_manager.class.php';
require_once dirname(__FILE__).'/../../rights_utilities.class.php';
require_once Path :: get_admin_path() . 'lib/rights_template_manager/rights_template_manager.class.php';
/**
 * Admin component
 */
class RightsManagerTemplaterComponent extends RightsManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('Rights')));
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RightsManager :: ACTION_EDIT_RIGHTS)), Translation :: get('EditRights')));
		$trail->add_help('rights general');
        
//        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
//        {
//            $this->display_header($trail);
//            $this->display_error_message(Translation :: get('NotAllowed'));
//            $this->display_footer();
//            exit();
//        }
        
        $package_manager = new RightsTemplateManager($this->get_parent());
        $package_manager->run();
    }
}
?>