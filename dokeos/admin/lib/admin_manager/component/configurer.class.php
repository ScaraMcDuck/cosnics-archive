<?php

/**
 * @package admin.lib.admin_manager.component
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../admin_manager.class.php';
require_once dirname(__FILE__).'/../admin_manager_component.class.php';
require_once dirname(__FILE__).'/../admin_search_form.class.php';
require_once dirname(__FILE__).'/../../configuration_form.class.php';
require_once dirname(__FILE__).'/../../admin_rights.class.php';
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
		$application = $this->application = Request :: get('application');
		if (!isset($application))
		{
			$application = $this->application = 'admin';
		}

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Settings')));

		if (!AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'settings', 'admin_manager_component'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$form = new ConfigurationForm($application, 'config', 'post', $this->get_url(array(AdminManager :: PARAM_APPLICATION => $application)));

		if($form->validate())
		{
			$success = $form->update_configuration();
			$this->redirect(Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_APPLICATION => $application));
		}
		else
		{
			$this->display_header($trail);
			echo $this->get_applications();
			$form->display();
			$this->display_footer();
		}
	}

	function get_applications()
	{
		$application = $this->application;

		$html = array();

		$html[] = '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/application.js' .'"></script>';
		$html[] = '<div class="configure">';

		WebApplication :: load_all();

		foreach ($this->get_application_platform_admin_links() as $application_links)
		{
			if (isset($application) && $application == $application_links['application']['class'])
			{
				$html[] = '<div class="application_current">';
			}
			else
			{
				$html[] = '<div class="application">';
			}
			$html[] = '<a href="'. $this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_CONFIGURE_PLATFORM, AdminManager :: PARAM_APPLICATION => $application_links['application']['class'])) .'">';
			$html[] = '<img src="'. Theme :: get_image_path() . 'place_' . $application_links['application']['class'] .'.png" border="0" style="vertical-align: middle;" alt="' . $application_links['application']['name'] . '" title="' . $application_links['application']['name'] . '"/><br />'. $application_links['application']['name'];
			$html[] = '</a>';
			$html[] = '</div>';
		}

		$html[] = '</div>';
		$html[] = '<div style="clear: both;"></div>';

		return implode("\n", $html);
	}
}
?>