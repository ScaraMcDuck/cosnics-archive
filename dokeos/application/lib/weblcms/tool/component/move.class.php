<?php

//require_once dirname(__PATH__).'../tool_component.class.php';

class ToolMoveComponent extends ToolComponent 
{
	function run() 
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$move = 0;
			if (isset($_GET[Tool::PARAM_MOVE]))
			{
				$move = $_GET[Tool::PARAM_MOVE];
			}
						
			$datamanager = WeblcmsDataManager :: get_instance();
			$publication = $datamanager->retrieve_learning_object_publication($_GET[Tool :: PARAM_PUBLICATION_ID]);
			if($publication->move($move))
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationMoved'));
				$this->redirect(null, $message, false, array());
			}
			$this->redirect(null, '', false, array());
		}
	}
}

?>