<?php
/**
 * @author Samumon
 */

require_once dirname(__FILE__) . '/../complex_display_component.class.php';

class GlossaryDisplayComponent extends ComplexDisplayComponent
{
	static function factory($component_name, $builder)
	{
		return parent :: factory('Glossary', $component_name, $builder);
	}
}

?>
