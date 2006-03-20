<?php
require_once dirname(__FILE__) . '/tool.class.php';
require_once dirname(__FILE__) . '/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__) . '/../../../../claroline/inc/lib/groupmanager.lib.php';

abstract class RepositoryTool extends Tool
{
	function RepositoryTool()
	{
		parent :: __construct();
		$this->set_parameter('tool', $_GET['tool']);
	}
	
	function get_groups($course, $user)
	{
		return GroupManager :: get_group_ids($course, $user);
	}
	
	function get_categories($course, $type)
	{
		return WebLCMSDataManager :: get_instance()->retrieve_publication_categories($course, $type);
	}
}
?>