<?php

require_once dirname(__FILE__) . '/../complex_builder.class.php';
require_once dirname(__FILE__) . '/survey_builder_component.class.php';

class SurveyBuilder extends ComplexBuilder
{
	function run()
	{
		$action = $this->get_action();
		
		switch($action)
		{
			case ComplexBuilder :: ACTION_BROWSE_CLO :
				$component = SurveyBuilderComponent :: factory('Browser', $this); 
				break;
		}
		
		if(!$component)
			parent :: run();
		else
			$component->run();
	}
}

?>