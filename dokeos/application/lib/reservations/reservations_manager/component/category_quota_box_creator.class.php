<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../quota_box_rel_category.class.php';
require_once dirname(__FILE__).'/../../forms/category_quota_box_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';

class ReservationsManagerCategoryQuotaBoxCreatorComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$category_id = $this->get_category_id();
		
		$trail = new BreadcrumbTrail();
		$admin = new Admin();
		$trail->add(new Breadcrumb($admin->get_link(array(Admin :: PARAM_ACTION => Admin :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORIES)), Translation :: get('View categories')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('ViewCategoryQuotaBoxes')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), Translation :: get('CreateCategoryQuotaBox')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$quota_box_rel_category = new QuotaBoxRelCategory();
		$quota_box_rel_category->set_category_id($category_id);
		
		$form = new CategoryQuotaBoxForm(CategoryQuotaBoxForm :: TYPE_CREATE, $this->get_url(array(ReservationsManager :: PARAM_CATEGORY_ID => $category_id)), $quota_box_rel_category, $user);

		if($form->validate())
		{
			$success = $form->create_quota_box_rel_category();
			$this->redirect('url', Translation :: get($success ? 'QuotaBoxAdded' : 'QuotaBoxNotAdded'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_CATEGORY_QUOTA_BOXES, ReservationsManager :: PARAM_CATEGORY_ID => $category_id));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
	}
	
	function get_category_id()
	{
		$id = Request :: get(ReservationsManager :: PARAM_CATEGORY_ID);
		if(!isset($id) || is_null($id))
			$id = 0;
		
		return $id;
	}
}
?>