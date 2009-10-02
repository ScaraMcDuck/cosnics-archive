<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';

class LearningPathBuilderComponent extends ComplexBuilderComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('LearningPath', $component_name, $builder);
	}
	
	function get_prerequisites_url($selected_cloi)
	{
		return $this->get_parent()->get_prerequisites_url($selected_cloi);
	}
	
	function get_mastery_score_url($selected_cloi)
	{
		return $this->get_parent()->get_mastery_score_url($selected_cloi);
	}
}

?>
