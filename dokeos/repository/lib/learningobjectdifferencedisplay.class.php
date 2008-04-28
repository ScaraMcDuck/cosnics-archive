<?php
/**
 * $Id$
 * @package repository
 * 
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
 require_once dirname(__FILE__).'/differenceengine.class.php';
/**
 * This class can be used to display the differences between two versions of a
 * learning object.
 */
class LearningObjectDifferenceDisplay {

	/**
	 * The learning object difference.
	 */
	private $difference;
	/**
	 * Constructor
	 * @param LearningObjectDifference $difference The learning object
	 * difference
	 */
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
		$html[] = '<div class="difference" style="background-image: url('.Theme :: get_common_img_path() . 'learning_object/' . $diff->get_object()->get_icon_name().'.png);">';
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
 			if (get_class($d) == 'Difference_Engine_Op_change')
			{
				$td = new Difference_Engine(explode(" ", $d->get_orig()), explode(" ", $d->get_final()));
				
				$html[] = '<div class="left">';
				$html_change = array();
				foreach($td->getDiff() as $t)
				{
					$html_change[] = $t->parse('final', true);
				}
				$html[] = implode(' ', $html_change);
				$html[] = '</div>';
				
				$html[] = '<div class="right">';
				$html_change = array();
				foreach($td->getDiff() as $t)
				{
					$html_change[] = $t->parse('orig', true);
				}
				$html[] = implode(' ', $html_change);
				$html[] = '</div>';
				
				$html[] = '<br style="clear:both;" />';
			}
			else
			{
				$html[] = '<div class="left">';
				$html[] = print_r($d->parse('final'), true) . '';
				$html[] = '</div>';
				$html[] = '<div class="right">';
				$html[] = print_r($d->parse('orig'), true) . '';
				$html[] = '</div>';
				$html[] = '<br style="clear:both;" />';
			}
		}
		$html[] = '</div>';

		return implode("\n", $html);
	}
	/**
	 * Gets the difference associated with this display class
	 * @return LearningObjectDifference
	 */
	function get_difference()
	{
		return $this->difference;
	}
	/**
	 * Returns the legend explaining the different sections in the difference
	 * display
	 * @return string
	 */
	function get_legend()
	{
		$html = array();
		$html[] = '<div class="learning_object" style="background-image: url('.Theme :: get_common_img_path().'place_legend.png);">';
		$html[] = '<div class="title">'. Translation :: get('Legend') .'</div>';
		$html[] = '<span class="compare_delete">'. Translation :: get('CompareExample') .'</span>: '. Translation :: get('CompareDeleteInfo') .'<br />';
		$html[] = '<span class="compare_add">'. Translation :: get('CompareExample') .'</span>: '. Translation :: get('CompareAddInfo') .'<br />';
		$html[] = '<span class="compare_change">'. Translation :: get('CompareExample') .'</span>: '. Translation :: get('CompareChangeInfo') .'<br />';
		$html[] = '<span class="compare_copy">'. Translation :: get('CompareExample') .'</span>: '. Translation :: get('CompareCopyInfo') .'<br />';
		$html[] = '</div>';

		return implode("\n", $html);
	}
	/**
	 * Creates a new learning object difference display class
	 * @param LearningObjectDifference $difference
	 */
    function factory($difference)
	{
		$type = $difference->get_object()->get_type();
		$class = LearningObject :: type_to_class($type).'DifferenceDisplay';
		require_once dirname(__FILE__).'/learning_object/'.$type.'/'.$type.'_differencedisplay.class.php';
		return new $class($difference);
	}
	
	function get_path($path_type)
	{
		return Path :: get($path_type);
	}

}
?>