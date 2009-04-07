<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ForumBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Forum', $component_name, $builder);
	}
}

?>
