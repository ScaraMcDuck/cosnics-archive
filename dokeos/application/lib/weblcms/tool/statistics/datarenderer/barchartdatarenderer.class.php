<?php
require_once dirname(__FILE__).'/../datarenderer.class.php';
class BarChartDataRenderer extends DataRenderer
{
	function BarChartDataRenderer($parent,$data)
	{
		parent::DataRenderer($parent,$data);
	}
    function display() {
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
			$bar = '<img src="'.api_get_path(WEB_CODE_PATH).'img/bar_1u.gif" width="'.$width.'" height="10" alt="'.$key.'"/>';
			$percent = number_format(100*$value/$sum,2).'%';
			$table_data[]  = array($key,$bar,$value,$percent);
		}
		$table = new SortableTableFromArray($table_data);
		$table->set_additional_parameters($this->parent->get_parameters());
		$table->set_header(0,get_lang('Tool'));
		$table->set_header(1,'',false);
		$table->set_header(2,get_lang('Amount'));
		$table->set_header(3,get_lang('Percentage'));
		$table->display();
    }
}
?>