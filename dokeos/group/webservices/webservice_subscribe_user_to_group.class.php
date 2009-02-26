<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/group_rel_user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceSubscribeUserGroup();
$handler->run();

class WebServiceSubscribeUserGroup
{
	private $webservice;
	private $functions;
	
	function WebServiceSubscribeUserGroup()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['subscribe_user'] = array(
			'input' => new GroupRelUser(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function subscribe_user($input_group_rel_user)
	{
		$gru = new GroupRelUser($input_group_rel_user[group_id],$input_group_rel_user[user_id]);
		$success = new ActionSuccess();
		$success->set_success($gru->create());
		return $success->get_default_properties();
	}
}