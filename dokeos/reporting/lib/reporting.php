<?php
/*
 * Receives a request, makes the reporting block retrieve its data & displays the block in the given format;
 * 
 * @author: Michael Kyndt
 */

require_once("reporting_formatter.php");

class Reporting{
	public static function generate_block($reporting_block){
		$reporting_block->retrieve_data();
		
 		$html .= "<div class=\"reporting_block\" style=\"height: ".$reporting_block->get_reportingblocklayout()->get_height()."px; width: ".$reporting_block->get_reportingblocklayout()->get_width()."px\">";
 		$html .= "<div class=\"reporting_header\">";
 		$html .= '[=Reporting=DisplayMode=] ';
 		$html .= "<select name=\"charttype\">";
 		foreach($reporting_block->get_displaymodes() as $key => $value)
 		{
 			$html .= "<option value=".$key.">".$value."</option>";
 		}
 		$html .= "</select>";
 		$html .= "</div>";
 		$html .= "<div class=\"reporting_content\">";
 		$html .= ReportingFormatter :: get_instance(&$reporting_block)->to_html();
 		$html .= "</div>";
 		$html .= "<div class=\"reporting_footer\"> .";
 		$html .= "</div>";
 		$html .= "</div>";
 		
 		return $html;
	}//generate_block
}//class reporting
?>