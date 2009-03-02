<?php
require_once(dirname(__FILE__) . '/../../common/global.inc.php');
require_once dirname(__FILE__) . '/../../common/webservices/webservice.class.php';
require_once dirname(__FILE__) . '/provider/input_group.class.php';
require_once dirname(__FILE__) . '/../lib/group.class.php';
require_once dirname(__FILE__) . '/../lib/data_manager/database.class.php';
require_once dirname(__FILE__) . '/../../common/webservices/action_success.class.php';


$handler = new WebServicesGroup();
$handler->run();

class WebServicesGroup
{
	private $webservice;
	private $functions;
	
	function WebServicesGroup()
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
		
		$functions['create_group'] = array(
			'input' => new Group(),
			'output' => new ActionSuccess()
		);
		
		$functions['delete_group'] = array(
			'input' => new Group(),
			'output' => new ActionSuccess()
		);
		
		$functions['subscribe_user'] = array(
			'input' => new GroupRelUser(),
			'output' => new ActionSuccess()
		);
		
		$functions['unsubscribe_user'] = array(
			'input' => new GroupRelUser(),
			'output' => new ActionSuccess()
		);
		
		$functions['update_group'] = array(
			'input' => new Group(),
			'output' => new ActionSuccess()
		);
		
		
		$this->webservice->provide_webservice($functions);

	}
	
	function get_group($input_group)
	{
		$gdm = DatabaseGroupDataManager :: get_instance();
		$group = $gdm->retrieve_group($input_group[id]);
		return $group->get_default_properties();
	}
	
	function create_group($input_group)
	{
		$g = new Group(0,$input_group);
		$success = new ActionSuccess();
		$success->set_success($g->create());
		return $success->get_default_properties();
	}
	
	function delete_group($input_group)
	{
		$g = new Group(0,$input_group);
		$success = new ActionSuccess();
		$success->set_success($g->delete());
		return $success->get_default_properties();
	}
	
	function subscribe_user($input_group_rel_user)
	{
		$gru = new GroupRelUser($input_group_rel_user[group_id],$input_group_rel_user[user_id]);
		$success = new ActionSuccess();
		$success->set_success($gru->create());
		return $success->get_default_properties();
	}
	
	function unsubscribe_user($input_group_rel_user)
	{
		$gru = new GroupRelUser($input_group_rel_user[group_id],$input_group_rel_user[user_id]);
		$success = new ActionSuccess();
		$success->set_success($gru->delete());
		return $success->get_default_properties();
	}
	
	function update_group($input_group)
	{
		$g = new Group(0,$input_group);
		$success = new ActionSuccess();
		$success->set_success($g->update());
		return $success->get_default_properties();
	}
	
}