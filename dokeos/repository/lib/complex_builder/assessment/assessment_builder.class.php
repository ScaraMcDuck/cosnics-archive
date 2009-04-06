<?php

require_once dirname(__FILE__) . '/../complex_builder.class.php';
require_once dirname(__FILE__) . '/assessment_builder_component.class.php';

class AssessmentBuilder extends ComplexBuilder
{
	function run()
	{
		$action = $this->get_action();
		
		switch($action)
		{
			case ComplexBuilder :: ACTION_BROWSE_CLO :
				$component = AssessmentBuilderComponent :: factory('Browser', $this); 
				break;
		}
		
		if(!$component)
			parent :: run();
		else
			$component->run();
	}
}

?>