<?php
require_once dirname(__FILE__) . '/tool.class.php';

abstract class RepositoryTool extends Tool
{
	function RepositoryTool()
	{
		parent :: __construct();
		$this->set_parameter('tool', $_GET['tool']);
	}
}
?>