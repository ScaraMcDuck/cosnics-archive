<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';

$handler = new WebServiceGetGroup();
$handler->run();

class WebServiceGetGroup
{
	private $webservice;
	private $functions;
	
	function WebServiceGetGroup()
	{
		$this->webservice = Webservice :: factory($this);
	}
	
	function run()
	{	
		$functions = array();
		
		$functions['get_group'] = array(
			'input' => new InputGroup(),
			'output' => new Group()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function get_group($input_group)
	{
		$gdm = DatabaseGroupDataManager :: get_instance();
		$group = $gdm->retrieve_group($input_group[id]);
		return $group->get_default_properties();
	}
	
}