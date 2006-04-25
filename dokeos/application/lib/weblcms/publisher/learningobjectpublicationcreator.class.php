<?php
require_once dirname(__FILE__).'/../learningobjectpublisher.class.php';
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../weblcmsdatamanager.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationform.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobjectdisplay.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobjectform.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositoryutilities.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/course.lib.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/groupmanager.lib.php';

class LearningObjectPublicationcreator extends LearningObjectPublisherComponent
{
	function as_html()
	{
		$oid = $_GET[LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID];
		if ($oid)
		{
			if ($_GET[LearningObjectPublisher :: PARAM_EDIT])
			{
				return $this->get_editing_form($oid);
			}
			return $this->get_publication_form($oid);
		}
		else
		{
			$type = $this->get_type();
			if ($type)
			{
				return $this->get_creation_form($type);
			}
			else
			{
				return $this->get_type_selector();
			}
		}
	}

	function get_type()
	{
		$types = $this->get_types();
		return (count($types) == 1 ? $types[0] : $_REQUEST['type']);
	}

	private function get_type_selector()
	{
		$types = array ();
		foreach ($this->get_types() as $t)
		{
			$types[$t] = $t;
		}
		$form = new FormValidator('selecttype', 'get');
		$form->addElement('hidden', 'tool');
		$form->addElement('hidden', LearningObjectPublisher :: PARAM_ACTION);
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', get_lang('Ok'));
		$form->setDefaults(array ('tool' => $_GET['tool'], LearningObjectPublisher :: PARAM_ACTION => $_GET[LearningObjectPublisher :: PARAM_ACTION]));
		return $form->asHtml();
	}

	private function get_creation_form($type)
	{
		$form = LearningObjectForm :: factory($type, 'create', 'post', $this->get_url());
		$form->build_creation_form($this->get_default_learning_object($type));
		$form->addElement('hidden', 'type');
		$form->setDefaults(array ('type' => $type));
		if ($form->validate())
		{
			$object = $form->create_learning_object($this->get_user_id());
			return $this->get_publication_form($object->get_id(), true);
		}
		else
		{
			return $form->toHTML();
		}
	}

	private function get_editing_form($objectID)
	{
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$form = LearningObjectForm::factory($object->get_type(),'edit','post',$this->get_url(array(LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $objectID, LearningObjectPublisher :: PARAM_EDIT => 1)));
		$form->build_editing_form(& $object);
		if ($form->validate())
		{
			$object = $form->create_learning_object($this->get_user_id());
			return $this->get_publication_form($object->get_id(), true);
		}
		else {
			return $form->toHtml();
		}
	}

	private function get_publication_form($objectID, $new = false)
	{
		$out = ($new ? Display :: display_normal_message(get_lang('ObjectCreated'), true) : '');
		$tool = $this->get_parent()->get_parent();
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$form = new LearningObjectPublicationForm($object, $tool);
		if ($form->validate())
		{
			$form->create_learning_object_publication();
			$out .= Display :: display_normal_message(get_lang('ObjectPublished'), true);
		}
		else
		{
			$out .= LearningObjectDisplay :: factory($object)->get_full_html();
			$out .= $form->toHtml();
		}
		return $out;
	}
}
?>