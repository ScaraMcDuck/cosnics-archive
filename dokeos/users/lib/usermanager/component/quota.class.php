<?php
/**
 * @package user.usermanager
 */
require_once dirname(__FILE__).'/../usermanager.class.php';
require_once dirname(__FILE__).'/../usermanagercomponent.class.php';
require_once dirname(__FILE__).'/../userquotaform.class.php';
require_once dirname(__FILE__).'/../../usersdatamanager.class.php';

class UserManagerQuotaComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$user_id = $this->get_user_id();
		$breadcrumbs = array();
		$breadcrumbs[] = array ('url' => $this->get_url(), 'name' => get_lang('UserQuota'));
		if (!$this->get_user()->is_platform_admin()) 
		{
			api_not_allowed();
		}
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
	
			$user = $this->retrieve_user($id);
			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: display_error_message(get_lang("NotAllowed"));
				$this->display_footer();
				exit;
			}
			$form = new UserQuotaForm($user, $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $id)));

			if($form->validate())
			{
				$success = $form->update_quota();
				$this->redirect('url', get_lang($success ? 'UserQuotaUpdated' : 'UserQuotaNotUpdated'), ($success ? false : true), array(UserManager :: PARAM_ACTION => ACTION_BROWSE_USERS));
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