<?php

class LearningObjectDifferenceDisplay {

	/**
	 * The learning object difference.
	 */
	private $difference;
    
    protected function LearningObjectDifferenceDisplay($difference)
	{
		$this->difference = $difference;
	}

	/**
	 * Returns a full HTML view of the learning object.
	 * @return string The HTML.
	 */
	function get_diff_as_html()
	{
		$diff = $this->get_difference();
		$html = array();
		$html[] = '<div class="difference" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$diff->get_object()->get_icon_name().'.gif);">';			
		$html[] = '<div class="titleleft">';
		$html[] = $diff->get_object()->get_title();
		$html[] = date(" (d M Y, H:i:s O)",$diff->get_object()->get_creation_date());
		$html[] = '</div>';
		$html[] = '<div class="titleright">';
		$html[] = $diff->get_version()->get_title();
		$html[] = date(" (d M Y, H:i:s O)",$diff->get_version()->get_creation_date());
		$html[] = '</div>';
		foreach($diff->get_difference() as $d)
 		{
			$html[] = '<div class="left">';
			$html[] = print_r($d->parse('orig'), true) . '';
			$html[] = '</div>';
			$html[] = '<div class="right">';
			$html[] = print_r($d->parse('final'), true) . '';
			$html[] = '</div>';
			$html[] = '<br style="clear:both;" />';
		}
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	function get_difference()
	{
		return $this->difference;
	}
    
    function factory(&$difference)
	{
		$type = $difference->get_object()->get_type();
		$class = LearningObject :: type_to_class($type).'DifferenceDisplay';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_differencedisplay.class.php';
		return new $class($difference);
	}
    
}
?>