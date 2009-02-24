<?php
/*
 * Format the reporting block into its given display mode
 * 
 * @author: Michael Kyndt
 */

 abstract class ReportingFormatter
 {	
 	private static $instance;
 	private $reporting_block;
 	
 	abstract function to_html();
 	
 	public static function get_instance(&$reporting_block)
 	{
 		$type = $reporting_block->get_displaymode();
 		if (!isset (self :: $instance))
		{
			if(strpos($type, 'Chart:') !== false)
			{
				$type = 'Chart';
			}
			require_once dirname(__FILE__).'/formatters/reporting_'.strtolower($type).'_formatter.class.php';
			$class = 'Reporting' . $type .'Formatter';
			self :: $instance = new $class ($reporting_block);
		}
		return self :: $instance;
 	}//get_instance
 	
 }//ReportingFormatter
?>