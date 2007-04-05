<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerCreatorComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$user = new User();
		
		$user_info = api_get_user_info();
		$user->set_creator_id($user_info['user_id']);
		
		$form = new UserForm(UserForm :: TYPE_CREATE, $user, $this->get_url());
		
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserCreate'));
		
		if($form->validate())
		{
			$success = $form->create_user();
			//$this->redirect(Weblcms :: ACTION_VIEW_WEBLCMS_HOME, get_lang($success ? 'CourseCreated' : 'CourseNotCreated'), ($success ? false : true));
		}
		else
		{
			$this->display_header($breadcrumbs);
			$form->display();
			$this->display_footer();
		}
	}
}
?>