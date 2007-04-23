<?php

abstract class PersonalMessengerDataManager {

	private static $instance;

    protected function PersonalMessengerDataManager()
    {
		$this->initialize();
    }
    
    static function get_instance()
    {
		if (!isset (self :: $instance))
		{
			$type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
			require_once dirname(__FILE__).'/data_manager/'.strtolower($type).'.class.php';
			$class = $type.'PersonalMessengerDataManager';
			self :: $instance = new $class ();
		}
		return self :: $instance;
    }
    
    abstract function get_next_personal_message_publication_id();
    
    abstract function initialize();
    
    abstract function count_personal_message_publications($condition = null);
    
    abstract function retrieve_personal_message_publications($condition = null, $orderBy = array (), $orderDir = array (), $offset = 0, $maxObjects = -1);
}
?>