<?php
class LearningObjectTableColumnModel
{
	private $columns;
	
	private $order_column;
	
	function LearningObjectTableColumnModel($columns, $default_order_column = 0)
	{
		$this->columns = $columns;
		$this->order_column = $default_order_column;
	}
	
	function get_column_count()
	{
		return count($this->columns);
	}
	
	function get_column($index)
	{
		return $this->columns[$index];
	}
	
	function add_column($column)
	{
		$this->columns[] = $column;
	}
	
	function get_default_order_column()
	{
		return $this->order_column;
	}
}
?>