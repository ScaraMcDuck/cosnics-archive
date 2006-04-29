<?php
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/../../abstractlearningobject.class.php';

class RepositoryManagerCreatorComponent extends RepositoryManagerComponent
{
	function run()
	{
		$type = $_REQUEST[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE];
		if ($type)
		{
			$object = new AbstractLearningObject($type, $this->get_user_id(), $_REQUEST[RepositoryManager :: PARAM_PARENT_LEARNING_OBJECT_ID]);
			$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE => $type)));
			if ($form->validate())
			{
				$object = $form->create_learning_object();
				$this->return_to_browser(get_lang('ObjectCreated'), $object->get_parent_id());
			}
			else
			{
				$this->display_header();
				$form->display();
				$this->display_footer();
			}
		}
		else
		{
			$this->display_error_page(get_lang('NoTypeSelected'));
		}
	}
}
?>