<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../classgroup_rel_user_table/classgrouprelusertable.class.php';
require_once dirname(__FILE__).'/classgroupreluserbrowsertabledataprovider.class.php';
require_once dirname(__FILE__).'/classgroupreluserbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/classgroupreluserbrowsertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../classgroupmanager.class.php';
/**
 * Table to display a set of learning objects.
 */
class ClassgroupRelUserBrowserTable extends ClassgroupRelUserTable
{
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function ClassgroupRelUserBrowserTable($browser, $name, $parameters, $condition)
	{
		$model = new ClassgroupRelUserBrowserTableColumnModel();
		$renderer = new ClassgroupRelUserBrowserTableCellRenderer($browser);
		$data_provider = new ClassgroupRelUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, $name, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[ClassgroupManager :: PARAM_UNSUBSCRIBE_SELECTED] = Translation :: get('UnsubsribeSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
}
?>