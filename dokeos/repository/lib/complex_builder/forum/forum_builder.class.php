<?php

require_once dirname(__FILE__) . '/../complex_builder.class.php';
require_once dirname(__FILE__) . '/forum_builder_component.class.php';

class ForumBuilder extends ComplexBuilder
{
    const ACTION_STICKY_CLOI = 'sticky_cloi';
    const ACTION_IMPORTANT_CLOI = 'important_cloi';

	function run()
	{
		$action = $this->get_action();
		
		switch($action)
		{
			case ComplexBuilder :: ACTION_BROWSE_CLO :
				$component = ForumBuilderComponent :: factory('Browser', $this); 
				break;
			case ComplexBuilder :: ACTION_CREATE_CLOI :
				$component = ForumBuilderComponent :: factory('Creator', $this); 
				break;
            case self :: ACTION_STICKY_CLOI :
                $component = ForumBuilderComponent :: factory('Sticky',$this);
                break;
            case self :: ACTION_IMPORTANT_CLOI :
                $component = ForumBuilderComponent :: factory('Important',$this);
                break;
		}
		
		if(!$component)
			parent :: run();
		else
			$component->run();
	}

    function get_complex_learning_object_item_sticky_url($cloi, $root_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_STICKY_CLOI,
                                    self :: PARAM_ROOT_LO => $root_id,
                                    self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id()));
    }

    function get_complex_learning_object_item_important_url($cloi, $root_id)
    {
        return $this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_IMPORTANT_CLOI,
                                    self :: PARAM_ROOT_LO => $root_id,
                                    self :: PARAM_SELECTED_CLOI_ID => $cloi->get_id()));
    }
}

?>