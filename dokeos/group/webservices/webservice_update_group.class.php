<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';


$handler = new WebServiceUpdateGroup();
$handler->run();

class WebServiceUpdateGroup
{
	private $webservice;
	private $functions;
	
	function WebServiceUpdateGroup()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['update_group'] = array(
			'input' => new Group(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

		//$this->webservice->provide_webservice_with_wsdl(dirname(__FILE__) . "/wsdl.xml");
	}
	
	function update_group($input_group)
	{
		$g = new Group();
		$g->set_default_properties($input_group);
		$gdm = DatabaseGroupDataManager :: get_instance();
		$success = new ActionSuccess();
		$success->set_success($gdm->update_group($g));
		return $success->to_array();	
	}
}