<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../../account_form.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerAccountComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		global $this_section;
		$this_section='myaccount';

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ModifyProfile')));

		$user = $this->get_user();

		$form = new AccountForm(AccountForm :: TYPE_EDIT, $user, $this->get_url());

		if($form->validate())
		{
			$success = $form->update_account();
			$this->redirect('url', Translation :: get($success ? 'UserProfileUpdated' : 'UserProfileNotUpdated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_ACCOUNT));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
}
?>