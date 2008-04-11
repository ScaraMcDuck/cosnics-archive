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
class HomeManagerEditorComponent extends HomeManagerComponent
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
		$trail = new BreadcrumbTrail();
		
		$trail->add(new Breadcrumb($this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), Translation :: get('Home')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('HomeEditor')));
		
		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: display_error_message(Translation :: get("NotAllowed"));
			$this->display_footer();
			exit;
		}
		
		if ($id && $type)
		{
			$url = $this->get_url(array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_EDIT_HOME, HomeManager :: PARAM_HOME_TYPE => $type, HomeManager :: PARAM_HOME_ID => $id));
			switch($type)
			{
				case HomeManager :: TYPE_BLOCK :
					$object = $this->retrieve_home_block($id);
					$form = new HomeBlockForm(HomeBlockForm :: TYPE_EDIT, $object, $url);
					break;
				case HomeManager :: TYPE_COLUMN :
					$object = $this->retrieve_home_column($id);
					$form = new HomeColumnForm(HomeColumnForm :: TYPE_EDIT, $object, $url);
					break;
				case HomeManager :: TYPE_ROW :
					$object = $this->retrieve_home_row($id);
					$form = new HomeRowForm(HomeRowForm :: TYPE_EDIT, $object, $url);
					break;
			}
			
			if ($form->validate())
			{
				$success = $form->update_object();
				$this->redirect('url', Translation :: get($success ? 'HomeUpdated' : 'HomeNotUpdated'), ($success ? false : true), array(HomeManager :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME));
			}
			else
			{
				//$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => $object->get_title());
				$this->display_header($trail);
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