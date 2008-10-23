<?php
/**
 * $Id: statistics_tool.class.php 15450 2008-05-27 12:03:12Z Scara84 $
 * Statistics tool
 * @package application.weblcms.tool
 * @subpackage statistics
 */
require_once dirname(__FILE__).'/../statistics_tool_component.class.php';
require_once 'HTML/Table.php';
require_once dirname(__FILE__) . '/renderers/data_renderer.class.php';

class StatisticsToolViewerComponent extends StatisticsToolComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$this->display_header($trail);
		$dm = WeblcmsDataManager :: get_instance();
		$parent = $this->get_parent();
		foreach ($parent->get_registered_tools() as $tool)
		{
			$number_of_publications = $dm->count_learning_object_publications($this->get_course_id(),null,null,null,new EqualityCondition('tool',$tool->name));
			$data[htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name).'Title'))] = $number_of_publications;
		}
		$renderer = DataRenderer :: factory('BarChart', $this, $data);
		$renderer->display();
		$this->display_footer();
	}
}
?>