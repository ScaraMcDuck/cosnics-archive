<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class WikiBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Wiki', $component_name, $builder);
	}
	
	function get_select_homepage_url()
	{
		return $this->get_parent()->get_select_homepage_url();
	}
}

?>
