<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class GlossaryBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Glossary', $component_name, $builder);
	}
}

?>
