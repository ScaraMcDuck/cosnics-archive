<?php
/**
 * $Id: editor.class.php 11337 2007-03-02 13:29:08Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../homemanager.class.php';
require_once dirname(__FILE__).'/../homemanagercomponent.class.php';
require_once dirname(__FILE__).'/../../homeblockform.class.php';
require_once dirname(__FILE__).'/../../homerowform.class.php';
require_once dirname(__FILE__).'/../../homecolumnform.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class HomeManagerCreatorComponent extends HomeManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='platform_admin';
		
		$type = $_GET[HomeManager :: PARAM_HOME_TYPE];
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), 'name' => Translation :: get('Home'));
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => Translation :: get('HomeCreator'));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($breadcrumbs);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if ($type)
		{
			$url = $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_CREATE_HOME, HomeManager :: PARAM_HOME_TYPE => $type));
			switch($type)
			{
				case HomeManager :: TYPE_BLOCK :
					$object = new HomeBlock();
					$form = new HomeBlockForm(HomeBlockForm :: TYPE_CREATE, $object, $url);
					break;
				case HomeManager :: TYPE_COLUMN :
					$object = new HomeColumn();
					$form = new HomeColumnForm(HomeColumnForm :: TYPE_CREATE, $object, $url);
					break;
				case HomeManager :: TYPE_ROW :
					$object = new HomeRow();
					$form = new HomeRowForm(HomeRowForm :: TYPE_CREATE, $object, $url);
					break;
			}
			
			if ($form->validate())
			{
				$success = $form->create_object();
				$this->redirect('url', Translation :: get($success ? 'HomeCreated' : 'HomeNotCreated'), ($success ? false : true), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
			}
			else
			{
				//$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => $object->get_title());
				$this->display_header($breadcrumbs);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>