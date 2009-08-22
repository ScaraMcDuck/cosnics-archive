<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__) . '/../admin_manager.class.php';
require_once dirname(__FILE__) . '/../admin_manager_component.class.php';
require_once dirname(__FILE__) . '/../admin_search_form.class.php';
require_once dirname(__FILE__) . '/../../configuration_form.class.php';
require_once dirname(__FILE__) . '/../../admin_rights.class.php';
/**
 * Admin component
 */
class AdminManagerConfigurerComponent extends AdminManagerComponent
{
    private $application;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $application = $this->application = Request :: get(AdminManager :: PARAM_WEB_APPLICATION);
        if (! isset($application))
        {
            $application = $this->application = 'admin';
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Settings')));
        $trail->add_help('administration');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'settings', 'admin_manager_component'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = new ConfigurationForm($application, 'config', 'post', $this->get_url(array(AdminManager :: PARAM_WEB_APPLICATION => $application)));
        
        if ($form->validate())
        {
            $success = $form->update_configuration();
            $this->redirect(Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_WEB_APPLICATION => $application));
        }
        else
        {
            $this->display_header($trail);
			$application_url = $this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_WEB_APPLICATION => Application :: PLACEHOLDER_APPLICATION));
			echo Application :: get_selecter($application_url, $this->application);
            $form->display();
            echo '<script language="JavaScript" type="text/javascript">';
            echo '$(document).ready(function() {';
            echo '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get('On') . '\', uncheckedLabel: \'' . Translation :: get('Off') . '\'});';
            echo '});';
            echo '</script>';
            $this->display_footer();
        }
    }
}
?>