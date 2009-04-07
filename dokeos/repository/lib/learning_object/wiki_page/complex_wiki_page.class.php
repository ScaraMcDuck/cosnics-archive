<?php
/**
 * @package repository.learningobject
 * @subpackage learning_path_chapter
 */
require_once dirname(__FILE__) . '/../../complex_learning_object_item.class.php';

class ComplexWikiPage extends ComplexLearningObjectItem
{
    const PROPERTY_IS_HOMEPAGE = 'is_homepage';

	static function get_additional_property_names()
	{
		return array(self :: PROPERTY_IS_HOMEPAGE);
	}

	function get_is_homepage()
	{
		return $this->get_additional_property(self :: PROPERTY_IS_HOMEPAGE);
	}

	function set_is_homepage($value)
	{
		$this->set_additional_property(self :: PROPERTY_IS_HOMEPAGE, $value);
	}
}
?>