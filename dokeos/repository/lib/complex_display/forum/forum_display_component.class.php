<?php
/**
 * @author Michael Kyndt
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class ForumDisplayComponent extends ComplexDisplayComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Forum', $component_name, $builder);
	}
}

?>
