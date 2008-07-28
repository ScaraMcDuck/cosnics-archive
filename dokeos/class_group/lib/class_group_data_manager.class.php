<?php
/**
 * $Id: repository_data_manager.class.php 11718 2007-03-27 09:52:32Z Scara84 $
 * @package repository
 */
require_once dirname(__FILE__).'/../../common/configuration/configuration.class.php';
require_once dirname(__FILE__).'/../../repository/lib/repository_data_manager.class.php';
/**
 *	This is a skeleton for a data manager for the Users table.
 *	Data managers must extend this class and implement its abstract methods.
 *
 *	@author Hans De Bisschop
 *	@author Dieter De Neef
 */
abstract class ClassGroupDataManager
{
	/**
	 * Instance of this class for the singleton pattern.
	 */
	private static $instance;

	/**
	 * Array which contains the registered applications running on top of this
	 * repositorydatamanager
	 */
	private $applications;

	/**
	 * Constructor.
	 */
	protected function ClassGroupDataManager()
	{
		
	}

	/**
	 * Uses a singleton pattern and a factory pattern to return the data
	 * manager. The configuration determines which data manager class is to
	 * be instantiated.
	 * @return GroupsDataManager The data manager.
	 */
	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'ClassGroupDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
	
}
?>