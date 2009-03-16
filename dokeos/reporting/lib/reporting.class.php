<?php
/**
 * Receives a request, makes the reporting block retrieve its data & displays the block in the given format;
 * 
 * @author: Michael Kyndt
 */

require_once("reporting_formatter.class.php");

class Reporting{
    /**
     * Generates a reporting block
     * @param ReportingBlock $reporting_block
     * @return html
     */
	public static function generate_block(&$reporting_block){
		//$reporting_block->retrieve_data();
 		$html[] = '<div id="'.$reporting_block->get_id().'" class="reporting_block"'.
 				'style="max-height:'.$reporting_block->get_height().'; width:'.$reporting_block->get_width().';\">';
 		$html[] = "<div class=\"reporting_header\">";
        $html[] = "<div class=\"reporting_header_title\">".Translation :: get($reporting_block->get_name())."</div>";
 		$html[] = Translation :: get('Displaymode').' ';
 		$html[] = "<select name=\"charttype\" class=\"charttype\"";
 		foreach($reporting_block->get_displaymodes() as $key => $value)
 		{
 			if($key == $reporting_block->get_displaymode())
 			{
 				$html[] = "<option SELECTED value=".$key.">".$value."</option>";
 			}else
 			{
 				$html[] = "<option value=".$key.">".$value."</option>";
 			}
 		}
 		$html[] = "</select>";
 		$html[] = "</div>";
 		$html[] = "<div class=\"reporting_content\">";
 		$html[] = ReportingFormatter :: factory($reporting_block)->to_html();
 		$html[] = "</div>";
 		$html[] = "<div class=\"reporting_footer\">";
        $html[] = Translation :: get('Export').':  O O O O';
 		$html[] = "</div>";
 		$html[] = "</div>";
 		
 		return implode("\n", $html);
	}//generate_block

    /**
     * Generates an array from a tracker
     * Currently only supports 1 serie
     * @todo support multiple series
     * @param Tracker $tracker
     * @return array
     */
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
        $datadescription["Description"]["Serie1"] = "Browsers";
		
		array_push($array,$data);
		array_push($array,$datadescription);
 		return $array;
	}//array_from_tracker
}//class reporting
?>