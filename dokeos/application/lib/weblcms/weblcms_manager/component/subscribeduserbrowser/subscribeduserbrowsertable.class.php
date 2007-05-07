<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../../../../users/lib/user_table/usertable.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/subscribeduserbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../../../users/lib/usermanager/usermanager.class.php';
/**
 * Table to display a set of learning objects.
 */
class SubscribedUserBrowserTable extends UserTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function SubscribedUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new SubscribedUserBrowserTableColumnModel();
		$renderer = new SubscribedUserBrowserTableCellRenderer($browser);
		$data_provider = new SubscribedUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[Weblcms :: PARAM_UNSUBSCRIBE_SELECTED] = get_lang('UnregisterSelected');
		
		if ($browser->get_course()->is_course_admin($browser->get_user_id()))
		{
			$this->set_form_actions($actions);
		}
		$this->set_default_row_count(20);
	}
}
?>