<?php
/**
 * $Id: editor.class.php 11337 2007-03-02 13:29:08Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../homemanager.class.php';
require_once dirname(__FILE__).'/../homemanagercomponent.class.php';
require_once dirname(__FILE__).'/../../homeblockconfigform.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerConfigurerComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$id = $_GET[HomeManager :: PARAM_HOME_ID];
		$trail = new BreadcrumbTrail();
		
		$trail->add(new Breadcrumb($this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeBlockConfigurer')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if ($id)
		{
			$url = $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_CONFIGURE_HOME, HomeManager :: PARAM_HOME_ID => $id));
			
			$object = $this->retrieve_home_block($id);
			$form = new HomeBlockConfigForm($object, $url);
			
			if ($form->validate())
			{
				$success = $form->update_block_config();
				$this->redirect('url', Translation :: get($success ? 'BlockConfigUpdated' : 'BlockConfigNotUpdated'), ($success ? false : true), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
			}
			else
			{
				$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Configure') . '&nbsp;' . $object->get_title()));
				$this->display_header($trail);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoHomeBlockSelected')));
		}
	}
}
?>