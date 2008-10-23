<?php
/**
 * $Id: statisticstool.class.php 10644 2007-01-10 13:13:43Z bmol $
 * Statistics tool: Bar chart renderer
 * @package application.weblcms.tool
 * @subpackage statistics
 */
require_once dirname(__FILE__).'/data_renderer.class.php';
class BarChartDataRenderer extends DataRenderer
{
	function BarChartDataRenderer($parent,$data)
	{
		parent::DataRenderer($parent,$data);
	}
	
    function display() 
    {
		$max = 0;
		$sum = 0;
		foreach($this->data as $key => $value)
		{
			$max = max($max,$value);
			$sum += $value;
		}
		foreach($this->data as $key => $value)
		{
			$width = round(400*$value/$max);
			$bar = '<img src="'.Theme :: get_common_img_path().'bar_1u.png" width="'.$width.'" height="10" alt="'.$key.'"/>';
			$percent = number_format(100*$value/$sum,2).'%';
			$table_data[]  = array($key,$bar,$value,$percent);
		}
		$table = new SortableTableFromArray($table_data);
		$table->set_additional_parameters($this->parent->get_parameters());
		$table->set_header(0,Translation :: get('Tool'));
		$table->set_header(1,'',false);
		$table->set_header(2,Translation :: get('Amount'));
		$table->set_header(3,Translation :: get('Percentage'));
		$table->display();
    }
}
?>