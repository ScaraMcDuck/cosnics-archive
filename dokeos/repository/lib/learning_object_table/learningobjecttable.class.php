<?php
require_once dirname(__FILE__).'/../../../claroline/inc/lib/sortabletable.class.php';
require_once dirname(__FILE__).'/defaultlearningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/defaultlearningobjecttablecellrenderer.class.php';

class LearningObjectTable
{
	const DEFAULT_NAME = 'learning_objects';
	const CHECKBOX_NAME_SUFFIX = '_id';
	
	private $table_name;

	private $default_row_count;

	private $column_model;

	private $data_provider;

	private $cell_renderer;
	
	private $additional_parameters;
	
	private $form_actions;

	function LearningObjectTable($data_provider, $table_name = null, $column_model = null, $cell_renderer = null)
	{
		$this->set_data_provider($data_provider);
		$this->set_name(isset($table_name) ? $table_name : self :: DEFAULT_NAME);
		$this->set_column_model(isset ($column_model) ? $column_model : new DefaultLearningObjectTableColumnModel());
		$this->set_cell_renderer(isset ($cell_renderer) ? $cell_renderer : new DefaultLearningObjectTableCellRenderer());
		$this->set_default_row_count(10);
		$this->set_additional_parameters(array());
	}

	function as_html()
	{
		$table = new SortableTable($this->get_name(), array ($this, 'get_learning_object_count'), array ($this, 'get_learning_objects'), $this->get_column_model()->get_default_order_column() + ($this->has_form_actions() ? 1 : 0), $this->get_default_row_count());
		$table->set_additional_parameters($this->get_additional_parameters());
		if ($this->has_form_actions())
		{
			$table->set_form_actions($this->get_form_actions(), $this->get_checkbox_name());
			$table->set_header(0, '', false);
		}
		$column_count = $this->get_column_model()->get_column_count();
		for ($i = 0; $i < $column_count; $i ++)
		{
			$column = $this->get_column_model()->get_column($i);
			$table->set_header(($this->has_form_actions() ? $i + 1 : $i), htmlentities($column->get_title()), $column->is_sortable()); 
		}
		return $table->as_html();
	}

	function get_learning_objects($offset, $count, $order_column, $order_direction)
	{
		$objects = $this->get_data_provider()->get_learning_objects($offset, $count, $this->get_column_model()->get_column($order_column - ($this->has_form_actions() ? 1 : 0))->get_learning_object_property(), $order_direction);
		$table_data = array ();
		$column_count = $this->get_column_model()->get_column_count();
		while ($object = $objects->next_result())
		{
			$row = array ();
			if ($this->has_form_actions())
			{
				$row[] = $object->get_id();
			}
			for ($i = 0; $i < $column_count; $i ++)
			{
				$row[] = $this->get_cell_renderer()->render_cell($this->get_column_model()->get_column($i), $object);
			}
			$table_data[] = $row;
		}
		return $table_data;
	}

	function get_learning_object_count()
	{
		return $this->get_data_provider()->get_learning_object_count();
	}
	
	function get_default_row_count()
	{
		return $this->default_row_count;
	}
	
	function set_default_row_count($default_row_count)
	{
		$this->default_row_count = $default_row_count;
	}
	
	function get_name()
	{
		return $this->table_name;
	}
	
	function set_name($name)
	{
		$this->table_name = $name;
	}
	
	function get_data_provider()
	{
		return $this->data_provider;
	}
	
	function set_data_provider($data_provider)
	{
		$this->data_provider = $data_provider;
	}
	
	function get_column_model()
	{
		return $this->column_model;
	}
	
	function set_column_model($model)
	{
		$this->column_model = $model;
	}
	
	function get_cell_renderer()
	{
		return $this->cell_renderer;
	}
	
	function set_cell_renderer($renderer)
	{
		$this->cell_renderer = $renderer;
	}
	
	function get_additional_parameters()
	{
		return $this->additional_parameters;
	}
	
	function set_additional_parameters($parameters)
	{
		$this->additional_parameters = $parameters;
	}
	
	function get_form_actions()
	{
		return $this->form_actions;
	}
	
	function set_form_actions($actions)
	{
		$this->form_actions = $actions;
	}
	
	function has_form_actions()
	{
		return count($this->get_form_actions());
	}
	
	function get_checkbox_name()
	{
		return $this->get_name().self :: CHECKBOX_NAME_SUFFIX;
	}
}
?>