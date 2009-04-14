<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path_chapter
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexWiki extends ComplexLearningObjectItem
{
    const PROPERTY_IS_LOCKED = 'is_locked';

	function get_allowed_types()
	{
		return array('wiki');
	}

    function get_is_locked()
	{
		return $this->get_additional_property(self :: PROPERTY_IS_LOCKED);
	}

    static function get_additional_property_names()
	{
		return array(self :: PROPERTY_IS_LOCKED);
	}

	function set_is_locked($value)
	{
		$this->set_additional_property(self :: PROPERTY_IS_LOCKED, $value);
	}
}
?>