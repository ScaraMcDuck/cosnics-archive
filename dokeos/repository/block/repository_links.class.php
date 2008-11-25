<?php
require_once dirname(__FILE__).'/../lib/repository_block.class.php';

class RepositoryLinks extends RepositoryBlock
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
		$html[] = '<ul class="attachments_list">';
		$html[] = '<li><img src="'. Theme :: get_common_img_path() . 'treemenu_types/link.png" />&nbsp;&nbsp;<a href="http://dokeos.ehb.be">Dokeos</a></li>';
		$html[] = '<li><img src="'. Theme :: get_common_img_path() . 'treemenu_types/profile.png" />&nbsp;&nbsp;<a href="http://svs.ehb.be">SVS</a></li>';
		$html[] = '<li><img src="'. Theme :: get_common_img_path() . 'treemenu_types/exercise.png" />&nbsp;&nbsp;<a href="http://dokeos.ehb.be/lassi">Lassi</a></li>';
		$html[] = '<li><img src="'. Theme :: get_common_img_path() . 'treemenu_types/wiki.png" />&nbsp;&nbsp;<a href="http://dokeos.ehb.be/courses/ADM-NCDIGIBIBL-SA/">Bibliotheek</a></li>';
		$html[] = '</ul>';
		$html[] = $this->display_footer();
		
		return implode("\n", $html);
	}
}
?>