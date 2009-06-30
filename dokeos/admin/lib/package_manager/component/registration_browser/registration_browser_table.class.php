<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_data_provider.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/package_manager/component/registration_browser/registration_browser_table_cell_renderer.class.php';

/**
 * Table to display a set of learning objects.
 */
class RegistrationBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'registration_browser_table';

	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function RegistrationBrowserTable($browser, $parameters, $condition)
	{
		$model = new RegistrationBrowserTableColumnModel();
		$renderer = new RegistrationBrowserTableCellRenderer($browser);
		$data_provider = new RegistrationBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, RegistrationBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[PackageManager :: PARAM_ACTIVATE_SELECTED] = Translation :: get('ActivateSelected');
		$actions[PackageManager :: PARAM_DEACTIVATE_SELECTED] = Translation :: get('DeactivateSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>