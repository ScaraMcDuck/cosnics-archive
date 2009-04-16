<?php
require_once dirname(__FILE__) . '/../../learning_object.class.php';
/**
 * @package repository.learningobject
 * @subpackage wiki
 */
class Wiki extends LearningObject 
{
    const PROPERTY_LOCKED = 'locked';
    
	function get_allowed_types()
	{
		return array('wiki_page');
	}

    function get_locked()
	{
		return $this->get_additional_property(self :: PROPERTY_LOCKED);
	}

	function set_locked($locked)
	{
		return $this->set_additional_property(self :: PROPERTY_LOCKED, $locked);
	}

    static function get_additional_property_names()
	{
		return array(self :: PROPERTY_LOCKED);
	}
}
?>