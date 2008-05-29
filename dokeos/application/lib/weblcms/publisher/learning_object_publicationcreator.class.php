<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage publisher
 */
require_once dirname(__FILE__).'/../learning_object_publisher.class.php';
require_once dirname(__FILE__).'/../learning_object_publisher_component.class.php';
require_once dirname(__FILE__).'/../weblcms_data_manager.class.php';
require_once dirname(__FILE__).'/../learning_object_publication_form.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a learning object publisher component which can be used
 * to create a new learning object before publishing it.
 */
class LearningObjectPublicationcreator extends LearningObjectPublisherComponent
{
	/*
	 * Inherited
	 */
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
			$types[$t] = $t;
		}
		$form = new FormValidator('selecttype', 'get');
		$form->addElement('hidden', 'tool');
		$form->addElement('hidden', LearningObjectPublisher :: PARAM_ACTION);
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$form->setDefaults(array ('tool' => $_GET['tool'], LearningObjectPublisher :: PARAM_ACTION => $_GET[LearningObjectPublisher :: PARAM_ACTION]));
		return $form->toHtml();
	}
	/**
	 * Gets the form to create the learning object.
	 * @return string A HTML-representation of the form.
	 */
	private function get_creation_form($type)
	{
		$default_lo = $this->get_default_learning_object($type);
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
	 * Gets the form to edit an existing learning object before publishing it.
	 * @return string A HTML-representation of the form.
	 */
	private function get_editing_form($objectID)
	{
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'edit', 'post', $this->get_url(array (LearningObjectPublisher :: PARAM_LEARNING_OBJECT_ID => $objectID, LearningObjectPublisher :: PARAM_EDIT => 1)));
		if ($form->validate())
		{
			$object = $form->create_learning_object();
			return $this->get_publication_form($object->get_id(), true);
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
	private function get_publication_form($objectID, $new = false)
	{
		$out = ($new ? Display :: display_normal_message(htmlentities(Translation :: get('ObjectCreated')), true) : '');
		$tool = $this->get_parent()->get_parent();
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$form = new LearningObjectPublicationForm($object, $tool, $this->get_parent()->with_mail_option(), $this->get_parent()->get_course());
		if ($form->validate())
		{
			$publication = $form->create_learning_object_publication();
			// TODO: Use a function for this.
			//$parameters['action'] = RepositoryTool::ACTION_SHOW_NORMAL_MESSAGE;
			$parameters['message'] = Translation :: get('ObjectPublished');
			$parameters['pcattree'] = $publication->get_category_id();
			$parameters['admin'] = 0;
			$url = $this->get_url($parameters);
			// Redirect to location where the publication was made
			header('Location: '.$url);
			// In case headers were allready sent, we simply show the confirmation message here
			$out .= Display::display_normal_message(Translation :: get('ObjectPublished'),true);
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