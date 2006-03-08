<?php
require_once dirname(__FILE__).'/../../../repository/lib/configuration.class.php';

abstract class WebLCMSDataManager
{
	private static $instance;

	protected function WebLCMSDataManager()
	{
		$this->initialize();
	}

	static function get_instance()
	{
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'WebLCMSDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
	}
}

?>