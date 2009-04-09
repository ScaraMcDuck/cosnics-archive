<?php

require_once dirname(__FILE__) . '/../complex_builder.class.php';
require_once dirname(__FILE__) . '/learning_path_builder_component.class.php';

class LearningPathBuilder extends ComplexBuilder
{
	const ACTION_CREATE_LP_ITEM = 'create_item';
	
	function run()
	{
		$action = $this->get_action();
		
		switch($action)
		{
			case ComplexBuilder :: ACTION_BROWSE_CLO :
				$component = LearningPathBuilderComponent :: factory('Browser', $this); 
				break;
			case LearningPathBuilder :: ACTION_CREATE_LP_ITEM :
				$component = LearningPathBuilderComponent :: factory('ItemCreator', $this); 
				break;	
			case self :: ACTION_DELETE_CLOI :
				$component = LearningPathBuilderComponent :: factory('Deleter', $this);
				break;
			case self :: ACTION_UPDATE_CLOI :
				$component = LearningPathBuilderComponent :: factory('Updater', $this);
				break;
		}
		
		if(!$component)
			parent :: run();
		else
			$component->run();
	}
}

?>