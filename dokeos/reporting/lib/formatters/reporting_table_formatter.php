<?php

 class ReportingTableFormatter extends ReportingFormatter
 {
 	private $reporting_block;
 	
 	public function to_html()
 	{
 		return "Succes! Here's your pretty table.";
 	}
 	
 	public function ReportingTableFormatter(&$reporting_block)
 	{
 		$this->reporting_block = $reporting_block;
 	}
 }//ReportingTextFormatter
?>
