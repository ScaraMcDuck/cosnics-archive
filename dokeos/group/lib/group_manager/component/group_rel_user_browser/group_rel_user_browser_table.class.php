<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table.class.php';
require_once dirname(__FILE__).'/group_rel_user_browser_table_data_provider.class.php';
require_once dirname(__FILE__).'/group_rel_user_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/group_rel_user_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../group_manager.class.php';
/**
 * Table to display a set of learning objects.
 */
class GroupRelUserBrowserTable extends ObjectTable
{
	const DEFAULT_NAME = 'group_browser_table';
	
	/**
	 * Constructor
	 * @see LearningObjectTable::LearningObjectTable()
	 */
	function GroupRelUserBrowserTable($browser, $parameters, $condition)
	{
		$model = new GroupRelUserBrowserTableColumnModel();
		$renderer = new GroupRelUserBrowserTableCellRenderer($browser);
		$data_provider = new GroupRelUserBrowserTableDataProvider($browser, $condition);
		parent :: __construct($data_provider, GroupRelUserBrowserTable :: DEFAULT_NAME, $model, $renderer);
		$this->set_additional_parameters($parameters);
		$actions = array();
		$actions[GroupManager :: PARAM_UNSUBSCRIBE_SELECTED] = Translation :: get('UnsubscribeSelected');
		$this->set_form_actions($actions);
		$this->set_default_row_count(20);
	}
	
	/**
	 * A typical ObjectTable would get the database-id of the object as a
	 * unique identifier. GroupRelUser has no such field since it's
	 * a relation, so we need to overwrite this function here.
	 */
	function get_objects($offset, $count, $order_column, $order_direction)
	{
		$classgrouprelusers = $this->get_data_provider()->get_objects($offset, $count, $this->get_column_model()->get_column($order_column - ($this->has_form_actions() ? 1 : 0))->get_object_property(), $order_direction);
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		while ($classgroupreluser = $classgrouprelusers->next_result())
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $classgroupreluser->get_classgroup_id() . '|' . $classgroupreluser->get_user_id();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $classgroupreluser);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}
}
?>