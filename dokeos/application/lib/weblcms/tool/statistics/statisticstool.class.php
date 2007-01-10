<?php
/**
 * $Id$
 * Statistics tool
 * @package application.weblcms.tool
 * @subpackage statistics
 */
require_once dirname(__FILE__).'/../tool.class.php';
require_once 'HTML/Table.php';
require_once 'datarenderer/barchartdatarenderer.class.php';

class StatisticsTool extends Tool
{
	function run()
	{
		$this->display_header();
		$dm = WeblcmsDataManager :: get_instance();
		$parent = $this->get_parent();
		foreach ($parent->get_registered_tools() as $tool)
		{
			$data[htmlspecialchars(get_lang(Tool :: type_to_class($tool->name).'Title'))] = rand(0,100);
		}
		$renderer = new BarChartDataRenderer($this,$data);
		$renderer->display();
		$this->display_footer();
	}
}
?>