<?php
require_once dirname(__FILE__).'/../learningobjectpublishercomponent.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject_form.class.php';

class LearningObjectCreator extends LearningObjectPublisherComponent
{
	function get_type()
	{
		$types = $this->get_types();
		return (count($types) == 1 ? $types[0] : $_REQUEST['type']);
	}

	function as_html()
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
		$form = LearningObjectForm :: factory($type, 'create', 'post', $_ENV['SCRIPT_NAME'].'?tool='.$_GET['tool'].'&publish_action='.$_GET['publish_action']);
		$form->build_create_form($type);
		$form->addElement('hidden', 'type');
		$form->setDefaults(array ('type' => $type));
		if ($form->validate())
		{
			$object = $form->create_learning_object(api_get_user_id());
			// TODO: Publish.
			var_dump($object);
		}
		else
		{
			return $form->asHtml();
		}
	}
}
?>