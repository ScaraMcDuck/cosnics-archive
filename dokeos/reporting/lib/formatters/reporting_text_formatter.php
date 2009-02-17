<?php
 
 class ReportingTextFormatter extends ReportingFormatter
 {
 	private $reporting_block;
 	
 	public function to_html()
 	{
 		return "Succes! Here's your pretty text.";
 	}
 	
 	public function ReportingTextFormatter(&$reporting_block)
 	{
 		$this->reporting_block = $reporting_block;
 	}
 }//ReportingTextFormatter
?>
