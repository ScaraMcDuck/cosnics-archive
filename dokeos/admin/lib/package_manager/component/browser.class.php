<?php
/**
 * @package admin
 * @subpackage package_manager
 * @author Hans De Bisschop
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table.class.php';
/**
 * Admin component
 */
class PackageManagerBrowserComponent extends PackageManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('PlatformAdmin')));
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Install')));
		$trail->add_help('administration install');

		if (!AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NotAllowed'));
			$this->display_footer();
			exit;
		}

		$this->display_header($trail);

		$table = new RegistrationBrowserTable($this, array(Application :: PARAM_ACTION => AdminManager :: ACTION_MANAGE_PACKAGES), $this->get_condition());
		echo $table->as_html();

		$this->display_footer();
	}

	function get_condition()
	{
	    return null;

//		$condition = new EqualityCondition(Group :: PROPERTY_PARENT, $this->get_group());
//
//		$query = $this->ab->get_query();
//		if(isset($query) && $query != '')
//		{
//			$or_conditions = array();
//			$or_conditions[] = new LikeCondition(Group :: PROPERTY_NAME, $query);
//			$or_conditions[] = new LikeCondition(Group :: PROPERTY_DESCRIPTION, $query);
//			$or_condition = new OrCondition($or_conditions);
//
//			$and_conditions[] = array();
//			$and_conditions = $condition;
//			$and_conditions = $or_condition;
//			$condition = new AndCondition($and_conditions);
//		}
//
//		return $condition;
	}
}
?>