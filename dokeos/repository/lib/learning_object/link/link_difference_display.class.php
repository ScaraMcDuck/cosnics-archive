<?php
/**
 * @package repository.learningobject
 * @subpackage link
 * 
 *  @author Hans De Bisschop
 *  @author Dieter De Neef
 */
/**
 * This class can be used to display the difference between links
 */
class LinkDifferenceDisplay extends LearningObjectDifferenceDisplay
{
	function get_diff_as_html()
	{
		$diff = $this->get_difference();
		
		$html = array();
		
		$html[] = '<div class="difference" style="background-image: url('.Theme :: get_common_image_path().$diff->get_object()->get_icon_name().'.png);">';			
		$html[] = '<div class="title">';
		$html[] = $diff->get_object()->get_title();
		$html[] = date(" (d M Y, H:i:s O)",$diff->get_object()->get_creation_date());
		$html[] = '</div>';
		
		foreach($diff->get_difference() as $d)
 		{
			$html[] = htmlspecialchars(print_r($d->parse('final'), true));
			$html[] = '<br style="clear:both;" />';
		}
		$html[] = '</div>';
		
		$html[] = '<div class="difference" style="background-image: url('.Theme :: get_common_image_path().$diff->get_version()->get_icon_name().'.png);">';			
		$html[] = '<div class="title">';
		$html[] = $diff->get_version()->get_title();
		$html[] = date(" (d M Y, H:i:s O)",$diff->get_version()->get_creation_date());
		$html[] = '</div>';
		
		foreach($diff->get_difference() as $d)
 		{
			$html[] = print_r($d->parse('orig'), true) . '';
			$html[] = '<br style="clear:both;" />';
		}
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}
?>