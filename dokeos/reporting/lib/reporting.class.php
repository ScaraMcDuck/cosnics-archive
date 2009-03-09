<?php
/**
 * Receives a request, makes the reporting block retrieve its data & displays the block in the given format;
 * 
 * @author: Michael Kyndt
 */

require_once("reporting_formatter.class.php");

class Reporting{
	public static function generate_block(&$reporting_block){
		$reporting_block->retrieve_data();
 		$html .= "<div id=\"".$reporting_block->get_id()."\" class=\"reporting_block\" " .
 				"style=\"height: ".$reporting_block->get_height()."px; " .
 						"width: ".$reporting_block->get_width()."px\">";
 		$html .= "<div class=\"reporting_header\">";
 		$html .= Translation :: get('Displaymode').' ';
 		$html .= "<select name=\"charttype\" class=\"charttype\"";
 		foreach($reporting_block->get_displaymodes() as $key => $value)
 		{
 			if($key == $reporting_block->get_displaymode())
 			{
 				$html .= "<option SELECTED value=".$key.">".$value."</option>";
 			}else
 			{
 				$html .= "<option value=".$key.">".$value."</option>";
 			}
 		}
 		$html .= "</select>";
 		$html .= "</div>";
 		$html .= "<div class=\"reporting_content\">";
 		$html .= ReportingFormatter :: get_instance($reporting_block)->to_html();
 		$html .= "</div>";
 		$html .= "<div class=\"reporting_footer\">";
 		$html .= "</div>";
 		$html .= "</div>";
 		
 		return $html;
	}//generate_block
	public static function array_from_tracker($tracker)
	{
		$c = 0;
    	$array = array();
    	$trackerdata = $tracker->export();
    	
    	foreach($trackerdata as $key => $value)
    	{
    		$data[$c]["Name"] = $value->get_name();
    		$data[$c]["Serie1"] = $value->get_value();
    		$c++;
    	}
    	
    	$datadescription["Position"] = "Name";
		$datadescription["Values"][] = "Serie1";
		
		array_push($array,$data);
		array_push($array,$datadescription);
 		return $array;
	}//array_from_tracker
}//class reporting
?>