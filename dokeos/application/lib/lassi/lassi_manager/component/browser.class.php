<?php
require_once dirname(__FILE__).'/../lassi.class.php';
require_once dirname(__FILE__).'/../lassi_component.class.php';

class LassiBrowserComponent extends LassiComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Lassi')));
		
		$this->display_header($trail);
		echo 'Test';
		$this->display_footer();
	}
}
?>