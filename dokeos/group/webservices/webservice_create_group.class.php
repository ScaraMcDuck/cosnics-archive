<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceCreateGroup();
$handler->run();

class WebServiceCreateGroup
{
	private $webservice;
	private $functions;
	
	function WebServiceCreateGroup()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['create_group'] = array(
			'input' => new Group(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function create_group($input_group)
	{
		$g = new Group(0,$input_group);
		$success = new ActionSuccess();
		$success->set_success($g->create());
		return $success->get_default_properties();
	}
}