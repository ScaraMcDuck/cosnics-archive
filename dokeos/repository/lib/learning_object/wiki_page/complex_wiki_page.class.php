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
	
	function update()
	{
		if($this->get_is_homepage())
		{
			$condition = new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $this->get_parent());
			//$conditions[] = new EqualityCondition(ComplexWikiPage :: PROPERTY_IS_HOMEPAGE, 1);
			//$condition = new AndCondition($conditions);
		
			$rdm = RepositoryDataManager :: get_instance();
			$children = $rdm->retrieve_complex_learning_object_items($condition);
			while($child = $children->next_result())
			{
				if($child->get_is_homepage())
				{
					$child->set_is_homepage(0);
					$child->update();
					break;
				}
			}
		}
		
		return parent :: update();
	}
}
?>