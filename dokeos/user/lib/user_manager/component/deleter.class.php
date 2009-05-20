<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_form.class.php';
require_once dirname(__FILE__).'/../../user_data_manager.class.php';

class UserManagerDeleterComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
			$user = $this->retrieve_user($id);

			if (!$this->get_user()->is_platform_admin())
			{
				$this->display_header();
				Display :: error_message(Translation :: get("NotAllowed"));
				$this->display_footer();
				exit;
			}

			$success = $user->delete();
			
			if($success)
    			Events :: trigger_event('delete', 'user', array('target_user_id' => $user->get_id(), 'action_user_id' => $this->get_user()->get_id()));
			
			$this->redirect(Translation :: get($success ? 'UserDeleted' : 'UserNotDeleted'), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));

		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>