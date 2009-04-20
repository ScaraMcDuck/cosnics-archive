<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class SurveyBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Survey', $component_name, $builder);
	}
}

?>
