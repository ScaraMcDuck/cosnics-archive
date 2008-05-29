<?php
/**
 * @package application.lib.encyclopedia.publisher
 */
require_once dirname(__FILE__).'/../publisher.class.php';
require_once dirname(__FILE__).'/../publisher_component.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_form.class.php';
require_once dirname(__FILE__).'/../../../../common/dokeos_utilities.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a encyclopedia publisher component which can be used
 * to create a new learning object before publishing it.
 */
abstract class PublisherPublicationcreatorComponent extends PublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		$oid = $_GET[Publisher :: PARAM_ID];
		if ($oid)
		{
			if ($_GET[Publisher :: PARAM_EDIT])
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
	/**
	 * Gets the type of the learning object which will be created.
	 */
	function get_type()
	{
		$types = $this->get_types();
		return (count($types) == 1 ? $types[0] : $_REQUEST['type']);
	}
	/**
	 * Gets the form to select a learning object type.
	 * @return string A HTML-representation of the form.
	 */
	private function get_type_selector()
	{
		$types = array ();
		foreach ($this->get_types() as $t)
		{
			$types[$t] = Translation :: get(LearningObject :: type_to_class($t).'TypeName');
		}
		$form = new FormValidator('selecttype', 'post', $this->get_url());
		$form->addElement('hidden', 'tool');
		$form->addElement('hidden', Publisher :: PARAM_ACTION);
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$form->setDefaults(array (Publisher :: PARAM_ACTION => $_GET[Publisher :: PARAM_ACTION]));
		
		if ($form->validate())
		{
			$values = $form->exportValues();			
			$type = $values['type'];
			return $this->get_creation_form($type);
		}
		else
		{
			return $form->toHTML();
		}
	}
	/**
	 * Gets the form to create the learning object.
	 * @return string A HTML-representation of the form.
	 */
	private function get_creation_form($type)
	{
		$default_lo = $this->get_default_object($type);
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $default_lo, 'create', 'post', $this->get_url(array ('type' => $type)));
		if ($form->validate())
		{
			$object = $form->create_learning_object();
			return $this->get_publication_form($object->get_id(), true);
		}
		else
		{
			return $form->toHTML();
		}
	}
	
	/**
	 * Gets the editing form
	 */
	private function get_editing_form($learning_object_id)
	{
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_object($learning_object_id);
		$form = ObjectForm :: factory(ObjectForm :: TYPE_REPLY, $learning_object, 'edit', 'post', $this->get_url(array (Publisher :: PARAM_ID => $learning_object_id, Publisher :: PARAM_EDIT => 1)));
		if ($form->validate())
		{
			$learning_object = $form->create_object();
			return $this->get_publication_form($learning_object->get_id(), true);
		}
		else
		{
			return $form->toHtml();
		}
	}

	/**
	 * Gets the form to publish the learning object.
	 * @return string|null A HTML-representation of the form. When the
	 * publication form was validated, this function will send header
	 * information to redirect the end user to the location where the
	 * publication was made.
	 */
	abstract function get_publication_form($learning_object_id, $new = false);
}
?>