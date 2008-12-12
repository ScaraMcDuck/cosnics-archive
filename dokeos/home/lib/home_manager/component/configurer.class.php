<?php
/**
 * $Id: editor.class.php 11337 2007-03-02 13:29:08Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../home_manager.class.php';
require_once dirname(__FILE__).'/../home_manager_component.class.php';
require_once dirname(__FILE__).'/../../home_block_config_form.class.php';
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
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if ($id)
		{
			$url = $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_CONFIGURE_HOME, HomeManager :: PARAM_HOME_ID => $id));
			
			$object = $this->retrieve_home_block($id);
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Configure') . '&nbsp;' . $object->get_title()));
			
			if ($object->is_configurable())
			{
				$form = new HomeBlockConfigForm($object, $url);
				
				if ($form->validate())
				{
					$success = $form->update_block_config();
					$this->redirect('url', Translation :: get($success ? 'BlockConfigUpdated' : 'BlockConfigNotUpdated'), ($success ? false : true), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
				}
				else
				{
					$this->display_header($trail);
					$form->display();
					$this->display_footer();
				}
			}
			else
			{
				$this->display_header($trail);
				$this->display_warning_message(Translation :: get('NothingToConfigure'));
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