<?php
/**
 * $Id$
 * Group tool
 * @package application.weblcms.tool
 * @subpackage group
 */
require_once dirname(__FILE__).'/../tool.class.php';

class GroupTool extends Tool
{
	function run()
	{
		$this->display_header();
		$dm = WeblcmsDataManager::get_instance();
		$course = $this->get_parent()->get_course();
		$groups = $dm->retrieve_groups($course->get_id());
		//TODO: implement the group tool
		echo '<ul>';
		while($group = $groups->next_result())
		{
			echo '<li>'.$group->get_name().'</li>';
		}
		echo '</ul>';
		$this->display_footer();
	}
}
?>