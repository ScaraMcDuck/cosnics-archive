<?php

require_once dirname(__FILE__).'../tool_component.class.php';

class ToolMoveSelectedToCategoryComponent extends ToolComponent
{
	
	function run() 
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$form = $this->build_move_to_category_form(self::ACTION_MOVE_SELECTED_TO_CATEGORY);
			$publication_ids = $_POST[self :: PARAM_PUBLICATION_ID];
			if (!is_array($publication_ids))
			{
				$publication_ids = array($publication_ids);
			}
			$form->addElement('hidden','pids',implode('-',$publication_ids));
			if($form->validate())
			{
				$values = $form->exportValues();
				$publication_ids = explode('-',$values['pids']);
				//TODO: update all publications in a single action/query
				foreach($publication_ids as $index => $publication_id)
				{
					$publication = $datamanager->retrieve_learning_object_publication($publication_id);
					$publication->set_category_id($_GET[LearningObjectPublication :: PROPERTY_CATEGORY_ID]);
					$publication->update();
				}
				if(count($publication_ids) == 1)
				{
					$message = Translation :: get('LearningObjectPublicationMoved');
				}
				else
				{
					$message = Translation :: get('LearningObjectPublicationsMoved');
				}
				$this->redirect(null, $message, false, array());
			}
			else
			{
				//$message = $form->toHtml();
				$this->display_header(new BreadCrumbTrail());
				$form->display();
				$this->display_footer();
			}
		}
	}
}

?>