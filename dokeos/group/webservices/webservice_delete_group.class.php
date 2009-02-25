<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceDeleteGroup();
$handler->run();

class WebServiceDeleteGroup
{
	private $webservice;
	private $functions;
	
	function WebServiceDeleteGroup()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['delete_group'] = array(
			'input' => new Group(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function delete_group($input_group)
	{
		$g = new Group(0,$input_group);
		$gdm = DatabaseGroupDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($gdm->delete_group($g));
		return $success->get_default_properties();	
	}
}