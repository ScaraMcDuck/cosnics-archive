<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/account_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';

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
			$this->redirect(Translation :: get($success ? 'UserProfileUpdated' : 'UserProfileNotUpdated'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_VIEW_ACCOUNT));
		}
		else
		{
			$this->display_header($trail, false, 'user general');
			$form->display();
			$this->display_footer();
		}
	}
}
?>