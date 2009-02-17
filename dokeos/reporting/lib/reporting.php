<?php
/*
 * Receives a request, makes the reporting block retrieve its data & displays the block in the given format;
 */

require_once("reporting_formatter.php");

class Reporting{
	public static function generate_block($reporting_block){
		$reporting_block->retrieve_data();
		return ReportingFormatter :: get_instance(&$reporting_block)->to_html();
	}//generate_report
}//class reporting
?>