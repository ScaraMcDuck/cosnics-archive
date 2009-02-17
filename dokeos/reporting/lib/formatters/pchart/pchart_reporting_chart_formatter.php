<?php

  require_once Path :: get_plugin_path().'/pChart/pChart/pChart.class';
  require_once Path :: get_plugin_path().'/pChart/pChart/pData.class';
  
  class PchartReportingChartFormatter extends ReportingChartFormatter
 {
 	private $instance;
	private $reporting_block;

	public function to_html()
	{
		return $this->get_pchart_instance()->to_html();
	} //to_html

	public function PchartReportingChartFormatter(&$reporting_block)
	{
		$this->reporting_block = $reporting_block;
	} //ReportingChartFormatter

	public function get_pchart_instance()
	{
		if (!isset (self :: $instance)) {
			$pos = strpos($this->reporting_block->get_displaymode(),':');
			$charttype = substr($this->reporting_block->get_displaymode(),$pos+1);
			require_once dirname(__FILE__) . '/'.strtolower($charttype) .'_pchart_reporting_chart_formatter.php';
			$class = $charttype.'PchartReportingChartFormatter';
			$this->instance = new $class($this->reporting_block);// (self :: $charttype);
		}
		return $this->instance;
	} //get_instance
 }
?>
