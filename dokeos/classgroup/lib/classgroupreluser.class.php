<?php

require_once dirname(__FILE__).'/classgroupdatamanager.class.php';
/**
 * @package users
 */
/**
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */

class ClassGroupRelUser
{
	const PROPERTY_CLASSGROUP_ID = 'classgroup_id';
	const PROPERTY_USER_ID = 'user_id';

	private $classgroup_id;
	private $user_id;

	function ClassGroupRelUser($classgroup_id = 0, $user_id = 0)
	{
		$this->classgroup_id = $classgroup_id;
		$this->user_id = $user_id;
	}
	
	function get_classgroup_id()
	{
		return $this->classgroup_id;
	}
	
	function set_classgroup_id($classgroup_id)
	{
		$this->classgroup_id = $classgroup_id;
	}
	
	function get_user_id()
	{
		return $this->user_id;
	}
	
	function set_user_id($user_id)
	{
		$this->user_id = $user_id;
	}	
	
	/**
	 * Instructs the Datamanager to delete this user.
	 * @return boolean True if success, false otherwise.
	 */
	function delete()
	{
		return ClassGroupDataManager :: get_instance()->delete_classgroup_rel_user($this);
	}
	
	function create()
	{
		$gdm = ClassGroupDataManager :: get_instance();
		return $gdm->create_classgroup_rel_user($this);
	}
}
?>