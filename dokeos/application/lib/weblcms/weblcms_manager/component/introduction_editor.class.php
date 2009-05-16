<?php

require_once dirname(__FILE__) . '/../weblcms_manager.class.php';
require_once dirname(__FILE__) . '/../weblcms_manager_component.class.php';
require_once dirname(__FILE__) . '/../../learning_object_publication_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class WeblcmsManagerIntroductionEditorComponent extends WeblcmsManagerComponent
{
	function run()
	{
		$publications = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publications($this->get_course_id(), null, null, null, new EqualityCondition('tool','introduction'));
		$introduction_text = $publications->next_result();
		
		$lo = $introduction_text->get_learning_object();
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $lo, 'edit', 'post', $this->get_url(array('edit_introduction' => $_GET['edit_introduction'])));

		if( $form->validate())
		{
			$form->update_learning_object();
			if($form->is_version())
			{	
				$introduction_text->set_learning_object($lo->get_latest_version());
				$introduction_text->update();
			}
			$this->redirect(null, Translation :: get('IntroductionEdited'), '', array(WeblcmsManager :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE));
		}
		else
		{
			$this->display_header();
			echo '<div class="clear"></div><br />';
			$form->display();
			$this->display_footer();
			exit();
		}
	}
}
?>