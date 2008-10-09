<?php

require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';

class ToolEditComponent extends ToolComponent
{
	function run()
	{
		if($this->is_allowed(EDIT_RIGHT))
		{
			$pid = isset($_GET[Tool :: PARAM_PUBLICATION_ID]) ? $_GET[Tool :: PARAM_PUBLICATION_ID] : $_POST[Tool :: PARAM_PUBLICATION_ID];
			$datamanager = WeblcmsDataManager :: get_instance();
			$publication = $datamanager->retrieve_learning_object_publication($pid);

			$form = new LearningObjectPublicationForm($publication->get_learning_object(),$this, false, $this->get_course(), false);
			$form->set_publication($publication);
			
			if( $form->validate())
			{
				$form->update_learning_object_publication();
				$message = htmlentities(Translation :: get('LearningObjectPublicationUpdated'));
				$this->redirect(null, $message, '', array());
			}
			else
			{
				$this->display_header(new BreadCrumbTrail());
				$form->display();
				$this->display_footer();
			}
		}
	}

}
?>