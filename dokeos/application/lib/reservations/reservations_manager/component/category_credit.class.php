<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../category.class.php';
require_once dirname(__FILE__).'/../../forms/credit_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerCategoryCreditComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $_GET[ReservationsManager :: PARAM_CATEGORY_ID];
		$trail = new BreadcrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('View items')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('SetCredits')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$categories = $this->retrieve_categories(new EqualityCondition(Category :: PROPERTY_ID, $category_id));
		$category = $categories->next_result();
		
		$form = new CreditForm( $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category->get_id())));

		if($form->validate())
		{
			$success = $form->set_credits_for_category($category);
			$this->redirect('url', Translation :: get($success ? 'CategoryCreditsApplied' : 'CategoryCreditsNotApplied'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS, ReservationsManager :: PARAM_CATEGORY_ID => $category->get_id()));
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