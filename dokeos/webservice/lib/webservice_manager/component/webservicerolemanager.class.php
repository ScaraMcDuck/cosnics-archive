<?php
/**
 * @package users.lib.usermanager.component
 */
require_once dirname(__FILE__).'/../webservice_manager.class.php';
require_once dirname(__FILE__).'/../webservice_manager_component.class.php';
//require_once dirname(__FILE__).'/../../forms/user_role_manager_form.class.php';
require_once dirname(__FILE__).'/../../webservice_data_manager.class.php';

class WebserviceManagerWebserviceRoleManagerComponent extends WebserviceManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Webservices')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('BrowseWebservices')));

		$webservice_id = Request :: get(WebserviceManager :: PARAM_WEBSERVICE_ID);
		if(!$webservice_id)
		{
			$this->display_header($trail);
			$this->display_error_message('NoObjectSelected');
			$this->display_footer();
			exit();
		}

		$webservice = $this->retrieve_webservice($webservice_id);

		$trail->add(new Breadcrumb($this->get_url(array(WebserviceManager :: PARAM_WEBSERVICE_ID => $webservice_id)), Translation :: get('ModifyWebserviceRoles')));

		//$form = new UserRoleManagerForm($user, $this->get_user(), $this->get_url(array(UserManager :: PARAM_USER_USER_ID => $user_id)));

		/*if($form->validate())
		{
			$success = $form->update_user_roles();
			$this->redirect('url', Translation :: get($success ? 'UserRolesChanged' : 'UserRolesNotChanged'), ($success ? false : true), array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS));
		}
		else
		{
			$this->display_header($trail);

			echo sprintf(Translation :: get('ModifyRolesForUser'), $webservice->get_name());

			$form->display();
			$this->display_footer();
		}*/
	}
}
?>

