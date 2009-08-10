<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../quota.class.php';
require_once dirname(__FILE__).'/../../forms/quota_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';

class ReservationsManagerQuotaCreatorComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)), Translation :: get('View quotas')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create quota')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$quota = new Quota();
		$form = new QuotaForm(QuotaForm :: TYPE_CREATE, $this->get_url(), $quota, $user);

		if($form->validate())
		{
			$success = $form->create_quota();
			$this->redirect(Translation :: get($success ? 'QuotaCreated' : 'QuotaNotCreated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
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