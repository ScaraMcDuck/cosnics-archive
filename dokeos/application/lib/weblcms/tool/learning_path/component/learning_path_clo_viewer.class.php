<?php

require_once Path :: get_repository_path() . 'lib/complex_display/complex_display.class.php';

class LearningPathToolCloViewerComponent extends LearningPathToolComponent
{
	function run()
	{
        $object_id = Request :: get('pid');
        $object = RepositoryDataManager :: get_instance()->retrieve_learning_object($object_id);
        $this->set_parameter(LearningPathTool :: PARAM_ACTION, LearningPathTool :: ACTION_VIEW_CLO);
		$display = ComplexDisplay :: factory($this, $object->get_type());
        $display->run();
	}

}
?>