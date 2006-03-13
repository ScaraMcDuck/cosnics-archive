<?php
class LearningObjectPublicationTable extends SortableTable
{
	private $browser;
	
	function LearningObjectPublicationTable($browser)
	{
		// TODO: Assign a dynamic table name.
		$name = 'pubtbl';
		parent :: __construct($name, array($browser, 'get_table_row_count'), array($browser, 'get_table_data'));
		$this->browser = $browser; 
	}
	
	function set_column_titles()
	{
		$titles = func_get_args();
		if (count($titles) == 1 && is_array($titles[0])) {
			$titles = $titles[0];
		}
		for ($column = 0; $column < count($titles); $column++)
		{
			$this->set_header($column, $titles[$column]);
		}
	}
}
?>