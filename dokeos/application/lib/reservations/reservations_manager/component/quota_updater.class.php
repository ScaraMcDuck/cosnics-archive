<?php
/**
 * @package reservations.lib.reservationsmanager.component
 */
require_once dirname(__FILE__).'/../reservations_manager.class.php';
require_once dirname(__FILE__).'/../reservations_manager_component.class.php';
require_once dirname(__FILE__).'/../../quota.class.php';
require_once dirname(__FILE__).'/../../forms/quota_form.class.php';
require_once dirname(__FILE__).'/../../reservations_data_manager.class.php';

class ReservationsManagerQuotaUpdaterComponent extends ReservationsManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$quota_id = $_GET[ReservationsManager :: PARAM_QUOTA_ID];
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
		$trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS)), Translation :: get('View quotas')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Update quota')));

		$user = $this->get_user();

		if (!isset($user)) 
		{
			Display :: display_not_allowed($trail);
			exit;
		}

		$quotas = $this->retrieve_quotas(new EqualityCondition(Quota :: PROPERTY_ID, $quota_id));
		$quota = $quotas->next_result();
		
		$form = new QuotaForm(QuotaForm :: TYPE_EDIT, $this->get_url(array(ReservationsManager :: PARAM_QUOTA_ID => $quota->get_id())), $quota, $user);

		if($form->validate())
		{
			$success = $form->update_quota();
			$this->redirect(Translation :: get($success ? 'QuotaUpdated' : 'QuotaNotUpdated'), ($success ? false : true), array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_QUOTAS));
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