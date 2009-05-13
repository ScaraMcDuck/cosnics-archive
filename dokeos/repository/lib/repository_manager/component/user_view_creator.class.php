<?php
/**
 * @package user_views.user_viewsmanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/user_view_form.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class RepositoryManagerUserViewCreatorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail(false);
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserViewList')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UserViewCreate')));

        $admin = new AdminManager();

		if (!$this->get_user()->is_platform_admin())
		{
			$this->display_header($trail);
			Display :: warning_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}
		$user_view = new UserView();
		$user_view->set_user_id($this->get_user_id());
		$form = new UserViewForm(UserViewForm :: TYPE_CREATE, $user_view, $this->get_url());
		
		if($form->validate())
		{
			$success = $form->create_user_view();
			$user_view = $form->get_user_view();
			
			$message = $success ? Translation :: get('UserViewCreated') : Translation :: get('UserViewNotCreated');
			
			$this->redirect(RepositoryManager :: ACTION_BROWSE_USER_VIEWS, $message, 0,  $success ? false: true, array());
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