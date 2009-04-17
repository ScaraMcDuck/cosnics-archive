<?php
/**
 * Receives a request, makes the reporting block retrieve its data & displays the block in the given format;
 * 
 * @author: Michael Kyndt
 */

require_once dirname(__FILE__).'/reporting_formatter.class.php';
require_once dirname(__FILE__).'/reporting_template.class.php';

class Reporting{
    const PARAM_ORIENTATION = 'orientation';

    const ORIENTATION_VERTICAL = 'vertical';
    const ORIENTATION_HORIZONTAL = 'horizontal';
    /**
     * Generates a reporting block
     * @param ReportingBlock $reporting_block
     * @return html
     */
	public static function generate_block(&$reporting_block,$params){
        if($params[ReportingTemplate :: PARAM_DIMENSIONS] == ReportingTemplate :: REPORTING_BLOCK_USE_CONTAINER_DIMENSIONS)
        {
            $html[] = '<div id="'.$reporting_block->get_id().'" class="reporting_block" style="max-height:'.$reporting_block->get_height().';">';
            $width = "<script>document.write(screen.width);</script>";
            //$reporting_block->set_width($width.'px');
        }else
        {
            $html[] = '<div id="'.$reporting_block->get_id().'" class="reporting_block" style="max-height:'.$reporting_block->get_height().';'.
            'width:'.$reporting_block->get_width().';">';
        }
 		$html[] = '<div class="reporting_header">';
        $html[] = '<div class="reporting_header_title">'.Translation :: get($reporting_block->get_name()).'</div>';
 		$html[] = '<div class="reporting_header_displaymode">';
 		$html[] = '<select name="charttype" class="charttype">';
 		foreach($reporting_block->get_displaymodes() as $key => $value)
 		{
 			if($key == $reporting_block->get_displaymode())
 			{
 				$html[] = '<option SELECTED value="'.$key.'">'.$value.'</option>';
 			}else
 			{
 				$html[] = '<option value="'.$key.'">'.$value.'</option>';
 			}
 		}
 		$html[] = '</select></div><div class="clear">&nbsp;</div>';
 		$html[] = '</div>';

 		$html[] = '<div class="reporting_content">';
 		$html[] = ReportingFormatter :: factory($reporting_block)->to_html();
 		$html[] = '</div>';

 		$html[] = '<div class="reporting_footer">';
        $html[] = '<div class="reporting_footer_export">';
        $html[] = $reporting_block->get_export_links();
        $html[] = '</div>&nbsp;<div class="clear">&nbsp;</div>';
 		$html[] = '</div>';
        
 		$html[] = '</div>';
 		
 		return implode("\n", $html);
	}//generate_block

    /**
     * Generates an array from a tracker
     * Currently only supports 1 serie
     * @todo support multiple series
     * @param Tracker $tracker
     * @return array
     */
	public static function array_from_tracker($tracker,$condition = null,$description = null)
	{
		$c = 0;
    	$array = array();
        $trackerdata = $tracker->retrieve_tracker_items($condition);
        
    	foreach($trackerdata as $key => $value)
    	{
            $data[$c]["Name"] = $value->get_name();
            $data[$c]["Serie1"] = $value->get_value();
            $c++;
    	}
    	
    	$datadescription["Position"] = "Name";
		$datadescription["Values"][] = "Serie1";
        $datadescription["Description"]["Serie1"] = $description;
		
		array_push($array,$data);
		array_push($array,$datadescription);
 		return $array;
	}//array_from_tracker

    public static function getSerieArray($arr,$description=null)
    {
        $array = array();
        $i = 0;
        if(!isset($arr))
        {
            $arr[''][] = Translation :: get('NotAvailable');
            unset($description);
        }
        foreach($arr as $key => $value)
        {
            $serie = 1;
            $data[$i]["Name"] = $key;
            foreach($value as $key2 => $value2)
            {
                $data[$i]["Serie".$serie] = $value2;
                $serie++;
            }
            $i++;
        }

        $datadescription["Position"] = "Name";
        $count = count($data[0])-1;
        for($i = 1;$i<=$count;$i++)
        {
            $datadescription["Values"][] = "Serie".$i;
            if($description && $count > 1 && count($description) < $count)
                $datadescription["Description"]["Serie".$i] = $description[$i];
            else if($description)
            {
                for($i = 0;$i<count($description);$i++)
                {
                    $datadescription["Description"]["Column".$i] = $description[$i];
                }
            }
        }
        if(isset($description[self::PARAM_ORIENTATION]))
            $datadescription[self::PARAM_ORIENTATION] = $description[self::PARAM_ORIENTATION];
        else
            $datadescription[self::PARAM_ORIENTATION] = ($serie-1 == 1)?self::ORIENTATION_VERTICAL:self::ORIENTATION_HORIZONTAL;

        array_push($array,$data);
        array_push($array,$datadescription);
        return $array;
    }//getSerieArray

    public static function sort_array(&$arr,$tesor)
    {
        arsort($arr[$tesor]);
        $i=0;
        foreach ($arr[$tesor] as $key => $value) {
            if($i < sizeof($arr[$tesor])/2)
            {
                foreach ($arr as $key2 => $value2) {
                    if($key2 != $tesor)
                    {
                        $bla = $arr[$key2][$key];
                        $arr[$key2][$key] = $arr[$key2][$i];
                        $arr[$key2][$i] = $bla;
                    }
                }
                $i++;
            }
        }
    }//sort_array
}//class reporting
?>