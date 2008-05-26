<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../classgroup_table/classgrouptable.class.php';
require_once dirname(__FILE__).'/classgroupbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/classgroupbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/classgroupbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../class_group_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ClassGroupBrowserTable extends ClassGroupTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function ClassGroupBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new ClassGroupBrowserTableColumnModel();
		$renderer = new ClassGroupBrowserTableCellRenderer($browser);
		$data_provider = new ClassGroupBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[ClassGroupManager :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
		$actions[ClassGroupManager :: PARAM_TRUNCATE_SELECTED] = Translation :: get('TruncateSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>