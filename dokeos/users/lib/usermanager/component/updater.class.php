<?php

require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerUpdaterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserUpdate'));
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
			$user = $this->retrieve_user($id);
		
			if (!api_is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(get_lang("NotAllowed"));
				$this->display_footer();
				exit;
			}
			
			$form = new Userform(UserForm :: TYPE_EDIT, $user, $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_user();
				$this->redirect(UserManager :: ACTION_UPDATE_USER, get_lang($success ? 'UserUpdated' : 'UserNotUpdated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
			}
			else
			{
				$this->display_header($breadcrumbs);
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
}
?>