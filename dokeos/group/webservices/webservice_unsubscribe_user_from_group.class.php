<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../lib/group_rel_user.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';

$handler = new WebServiceUnsubscribeUserGroup();
$handler->run();

class WebServiceUnsubscribeUserGroup
{
	private $webservice;
	private $functions;
	
	function WebServiceUnsubscribeUserGroup()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['unsubscribe_user'] = array(
			'input' => new GroupRelUser(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

		//$this->webservice->provide_webservice_with_wsdl(dirname(__FILE__) . "/wsdl.xml");
	}
	
	function unsubscribe_user($input_group_rel_user)
	{
		$gru = new GroupRelUser();
		$gru->set_default_properties($input_group_rel_user);
		$gdm = DatabaseGroupDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($gdm->delete_group_rel_user($gru));
		return $success->to_array();	
	}
}