<?php

require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../profileform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerProfileComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{	
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserProfile'));

		$user = $this->get_user();
		
		$form = new ProfileForm(ProfileForm :: TYPE_EDIT, $user, $this->get_url());

		if($form->validate())
		{
			$success = $form->update_profile();
			$this->redirect('url', get_lang($success ? 'UserProfileUpdated' : 'UserProfileNotUpdated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_PROFILE));
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