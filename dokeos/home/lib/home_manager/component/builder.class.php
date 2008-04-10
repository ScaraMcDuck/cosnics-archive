<?php
/**
 * @package groups.groupsmanager
 */
require_once dirname(__FILE__).'/../homemanager.class.php';
require_once dirname(__FILE__).'/../homemanagercomponent.class.php';
require_once dirname(__FILE__).'/../../homedatamanager.class.php';
require_once dirname(__FILE__).'/wizards/buildwizard.class.php';

class HomeManagerBuilderComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), 'name' => Translation :: get('Home'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('HomeBuilder'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}

		$bw = new BuildWizard($this);
		$bw->run(); 
	}
}
?>