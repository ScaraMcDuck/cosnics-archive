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
            echo $this->get_applications();
            $form->display();
            echo '<script language="JavaScript" type="text/javascript">';
            echo '$(document).ready(function() {';
            echo '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get('On') . '\', uncheckedLabel: \'' . Translation :: get('Off') . '\'});';
            echo '});';
            echo '</script>';
            $this->display_footer();
        }
    }

    function get_applications()
    {
        $application = $this->application;
        
        $tabs = array();
        $html = array();
        
        $index = 0;
        
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/application.js' . '"></script>';
        $html[] = '<div class="configure">';
//		$tabs[] = '<div id="tabs">';
//		$tabs[] = '<ul>';
        
        WebApplication :: load_all();
        
        foreach ($this->get_application_platform_admin_links() as $application_links)
        {
        	$settings_count = AdminDataManager :: get_instance()->count_settings(new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application_links['application']['class']));
        	if ($settings_count > 0)
            {
	        	$index ++;
//	            $tabs[] = '<li><a href="' . $this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_WEB_APPLICATION => $application_links['application']['class'])) . '">';
//	            $tabs[] = '<span class="category">';
//	            $tabs[] = '<img src="' . Theme :: get_image_path() . 'place_mini_' . $application_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/>';
//	            $tabs[] = '<span class="title">' . $application_links['application']['name'] . '</span>';
//	            $tabs[] = '</span>';
//	            $tabs[] = '</a></li>';
	        	
	            if (isset($application) && $application == $application_links['application']['class'])
	            {
	                $html[] = '<div class="application_current">';
	            }
	            else
	            {
	                $html[] = '<div class="application">';
	            }
	            $html[] = '<a href="' . $this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_WEB_APPLICATION => $application_links['application']['class'])) . '">';
	            $html[] = '<img src="' . Theme :: get_image_path() . 'place_' . $application_links['application']['class'] . '.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/><br />' . $application_links['application']['name'];
	            $html[] = '</a>';
	            $html[] = '</div>';
            }
        }
        
        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';
        
//        $tabs[] = '</ul>';
//        $tabs[] = '</div>';
//        $tabs[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/admin_ajax.js');
        
        return implode("\n", $html) . implode("\n", $tabs);
    }
}
?>