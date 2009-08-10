<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../category.class.php';
require_once dirname(__FILE__).'/../../forms/category_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';

class ReservationsManagerCategoryCreatorComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('View categories')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('Create category')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$category = new Category();
		$category->set_parent(isset($category_id)?$category_id:0);
		
		$form = new CategoryForm(CategoryForm :: TYPE_CREATE, $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $category, $user);

		if($form->validate())
		{
			$success = $form->create_category();
			$this->redirect(Translation :: get($success ? 'CategoryCreated' : 'CategoryNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_CATEGORIES, ReservationsManager :: PARAM_CATEGORY_ID => $category->get_id()));
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