<?php
/**
 * Format the reporting block into its given display mode
 * 
 * @author: Michael Kyndt
 */

 abstract class ReportingFormatter
 {	
    /**
     * Generates the html representing the chosen display mode
     * @return html
     */
 	abstract function to_html();
 	
 	public static function factory(&$reporting_block)
 	{
            $type = $reporting_block->get_displaymode();
			if(strpos($type, 'Chart:') !== false)
			{
				$type = 'Chart';
			}
			require_once dirname(__FILE__).'/formatters/reporting_'.strtolower($type).'_formatter.class.php';
			$class = 'Reporting' . $type .'Formatter';
		return new $class ($reporting_block);
 	}//get_instance
 	
 }//ReportingFormatter
?>