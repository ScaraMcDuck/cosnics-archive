<?php
/**
 * @package repository.learningobject
 * @subpackage portfolio
 */
class PortfolioItemDisplay extends LearningObjectDisplay
{

	function get_full_html()
	{
		$html = parent :: get_full_html();
		//$object = $this->get_learning_object();
//		return preg_replace('|</div>\s*$|s', '<div class="link_url" style="margin-top: 1em;"><a href="miep">miep</a></div></div>', $html);
		return $html;
	}
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object"><a href="moop">moop</a></span>';
	}

}
?>