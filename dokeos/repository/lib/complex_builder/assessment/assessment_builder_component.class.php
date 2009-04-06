<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class AssessmentBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Assessment', $component_name, $builder);
	}
}

?>
