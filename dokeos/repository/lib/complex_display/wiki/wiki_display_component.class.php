<?php
/**
 * @author Samumon
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class WikiDisplayComponent extends ComplexDisplayComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Wiki', $component_name, $builder);
	}
}

?>
