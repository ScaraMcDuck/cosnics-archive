<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class ForumTopicBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('ForumTopic', $component_name, $builder);
	}
}

?>
