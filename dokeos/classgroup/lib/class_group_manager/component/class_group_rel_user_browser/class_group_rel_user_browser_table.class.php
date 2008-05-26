<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../class_group_rel_user_table/class_group_rel_user_table.class.php';
require_once dirname(__FILE__).'/class_group_rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/class_group_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/class_group_rel_user_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../class_group_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ClassGroupRelUserBrowserTable extends ClassGroupRelUserTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function ClassGroupRelUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new ClassGroupRelUserBrowserTableColumnModel();
		$renderer = new ClassGroupRelUserBrowserTableCellRenderer($browser);
		$data_provider = new ClassGroupRelUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[ClassGroupManager :: PARAM_UNSUBSCRIBE_SELECTED] = Translation :: get('UnsubsribeSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>