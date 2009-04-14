<?php
/**
 * @package application.lib.calendar.publisher
 */
require_once dirname(__FILE__).'/../myportfolio_block.class.php';

/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class MyPortfolioExtra extends MyPortfolioBlock
{
	function run()
	{
		return $this->as_html();
	}
	
	/*
	 * Inherited
	 */
	function as_html()
	{
		$html = array();
		
		$html[] = $this->display_header();
		$html[] = '<ul>';
		$html[] = '<li><a href="run.php?application=myportfolio">'."My Portfolio".'</a></li>';
		$html[] = '<li><a href="http://pointcarre.vub.ac.be/docs/index.php/Doctoraatsportfolio">'."Manual".'</a></li>';
		$html[] = '</ul>';
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
}
?>
