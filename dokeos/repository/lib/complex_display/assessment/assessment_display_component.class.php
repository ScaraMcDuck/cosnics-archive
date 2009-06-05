<?php
/**
 * @author Sven Vanpoucke
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class AssessmentDisplayComponent extends ComplexDisplayComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Assessment', $component_name, $builder);
	}
}

?>
