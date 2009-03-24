<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../user_manager.class.php';
require_once dirname(__FILE__).'/../user_manager_component.class.php';

class UserManagerChangeUserComponent extends UserManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = $_GET[UserManager :: PARAM_USER_USER_ID];
		if ($id)
		{
			$success = true;
			$_SESSION['_uid'] = $id;
			$_SESSION['_as_admin'] = $this->get_user_id();
			header('Location: index.php');

		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>