<?php
/**
 * $Id: editor.class.php 11337 2007-03-02 13:29:08Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../homemanager.class.php';
require_once dirname(__FILE__).'/../homemanagercomponent.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerDeleterComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$id = $_GET[HomeManager :: PARAM_HOME_ID];
		$type = $_GET[HomeManager :: PARAM_HOME_TYPE];
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), 'name' => Translation :: get('Home'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('HomeDeleter'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if ($id && $type)
		{
			switch($type)
			{
				case HomeManager :: TYPE_BLOCK :
					$object = $this->retrieve_home_block($id);
					break;
				case HomeManager :: TYPE_COLUMN :
					$object = $this->retrieve_home_column($id);
					break;
				case HomeManager :: TYPE_ROW :
					$object = $this->retrieve_home_row($id);
					break;
			}
			
			if (!$object->delete())
			{
				$success = false;
			}
			else
			{
				$success = true;
			}
			
			$this->redirect('url', Translation :: get($success ? 'HomeDeleted' : 'HomeNotDeleted'), ($success ? false : true), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>