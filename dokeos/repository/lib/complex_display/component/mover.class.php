<?php
/**
 * @author Michael Kyndt
 */

class ComplexDisplayMoverComponent extends ComplexDisplayComponent
{
    function run()
	{
		if($this->get_parent()->get_parent()->is_allowed(EDIT_RIGHT))
		{
			$move = 0;
			if (isset($_GET[ComplexDisplay::PARAM_MOVE]))
			{
				$move = $_GET[ComplexDisplay::PARAM_MOVE];
			}

            $datamanager = RepositoryDataManager :: get_instance();
			$publication = $datamanager->retrieve_learning_object($_GET[Tool :: PARAM_PUBLICATION_ID]);
			if($publication->move($move))
			{
				$message = htmlentities(Translation :: get('LearningObjectPublicationMoved'));
			}
			$this->redirect($message, false, array());
		}
	}
}
?>
