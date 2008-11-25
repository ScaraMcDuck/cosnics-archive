<?php
require_once dirname(__FILE__).'/../lib/repository_block.class.php';

class RepositoryIntranet extends RepositoryBlock
{
	/**
	 * Runs this component and displays its output.
	 * This component is only meant for use within the home-component and not as a standalone item.
	 */
	function run()
	{
		return $this->as_html();
	}
	
	function as_html()
	{
		$html = array();
		$html[] = $this->display_header();	
		$html[] = '<ul style="padding: 0px; margin: 0px 0px 0px 15px;">';
		$html[] = '<li><a href="#">Afwezige docenten 24/11/2008</a></li>';
		$html[] = '<li><a href="#">Examenrooster beschikbaar</a></li>';
		$html[] = '<li><a href="#">Examenregeling</a></li>';
		$html[] = '<li><a href="#">Oefeningen Portugees 20/11/2008</a></li>';
		$html[] = '<li><a href="#">Dokeos opleiding(en)</a></li>';
		$html[] = '</ul>';
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
}
?>