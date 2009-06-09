<?php

//require_once dirname(__PATH__).'../tool_component.class.php';

class ToolMoveComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$move = 0;
			if (Request :: get(Tool::PARAM_MOVE))
			{
				$move = Request :: get(Tool::PARAM_MOVE);
			}

			$datamanager = WeblcmsDataManager :: get_instance();
			$publication = $datamanager->retrieve_learning_object_publication(Request :: get(Tool :: PARAM_PUBLICATION_ID));
			if($publication->move($move))
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationMoved'));
				//$this->redirect($message, false, array());
			}
			$this->redirect($message, false, array());
		}
	}
}

?>