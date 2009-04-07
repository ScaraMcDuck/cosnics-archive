<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class LearningPathBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('LearningPath', $component_name, $builder);
	}
}

?>
