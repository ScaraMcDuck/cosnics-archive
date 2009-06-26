<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class PortfolioBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Portfolio', $component_name, $builder);
	}
}

?>
