<?php

require_once dirname(__FILE__) . '/../complex_builder.class.php';
require_once dirname(__FILE__) . '/forum_topic_builder_component.class.php';

class ForumTopicBuilder extends ComplexBuilder
{
	function run()
	{
		$action = $this->get_action();
		
		switch($action)
		{
			case ComplexBuilder :: ACTION_BROWSE_CLO :
				$component = ForumTopicBuilderComponent :: factory('Browser', $this); 
				break;
		}
		
		if(!$component)
			parent :: run();
		else
			$component->run();
	}
}

?>