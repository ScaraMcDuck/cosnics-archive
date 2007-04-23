<?php

abstract class PersonalMessageDataManager {

	private static $instance;

    protected function PersonalMessageDataManager()
    {
		$this->initialize();
    }
    
    static function get_instance()
    {
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'PersonalMessageDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
    }
    
    abstract function initialize();
}
?>