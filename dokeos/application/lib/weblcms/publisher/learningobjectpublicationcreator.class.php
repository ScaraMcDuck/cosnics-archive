<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repositorydatamanager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_display.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_form.class.php';
require_once api_get_path(SYS_CODE_PATH).'/inc/lib/formvalidator/FormValidator.class.php';

class LearningObjectPublicationcreator extends LearningObjectPublisherComponent
{
	function as_html()
	{
		$oid = $_GET['object'];
		if ($oid)
		{
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
		$form->addElement('hidden', 'publish_action');
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', get_lang('OK'));
		$form->setDefaults(array ('tool' => $_GET['tool'], 'publish_action' => $_GET['publish_action']));
		return $form->asHtml();
	}

	private function get_creation_form($type)
	{
		$form = LearningObjectForm :: factory($type, 'create', 'post', $this->get_url());
		$form->build_create_form($type);
		$form->addElement('hidden', 'type');
		$form->setDefaults(array ('type' => $type));
		if ($form->validate())
		{
			$object = $form->create_learning_object(api_get_user_id());
			return $this->get_publication_form($object->get_id(), true);
		}
		else
		{
			return $form->toHTML();
		}
	}

	private function get_publication_form($objectID, $new = false)
	{
		$out = '';
		if ($new)
		{
			$out .= Display :: display_normal_message(get_lang('ObjectCreated'), true);
		}
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$out .= LearningObjectDisplay :: factory($object)->get_full_html();
		// TODO: Form with publication options (groups/user/timerange/...)
		$form = new FormValidator('create_publication', 'post', $this->get_url(array ('object' => $object->get_id())));
		$form->addElement('submit', 'submit', get_lang('Ok'));
		return $out.$form->toHtml();
	}
}
?>