<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../classgroup_table/classgrouptable.class.php';
require_once dirname(__FILE__).'/classgroupbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/classgroupbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/classgroupbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../classgroupmanager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ClassgroupBrowserTable extends ClassgroupTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function ClassgroupBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new ClassgroupBrowserTableColumnModel();
		$renderer = new ClassgroupBrowserTableCellRenderer($browser);
		$data_provider = new ClassgroupBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[ClassgroupManager :: PARAM_REMOVE_SELECTED] = Translation :: get('RemoveSelected');
		$actions[ClassgroupManager :: PARAM_TRUNCATE_SELECTED] = Translation :: get('TruncateSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>