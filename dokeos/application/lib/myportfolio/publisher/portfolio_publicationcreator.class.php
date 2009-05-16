<?php
/**
 * @package application.lib.portfolio.publisher
 */
require_once dirname(__FILE__).'/../portfolio_publisher.class.php';
require_once dirname(__FILE__).'/../portfolio_publisher_component.class.php';
require_once dirname(__FILE__).'/../portfolio_data_manager.class.php';
require_once dirname(__FILE__).'/../portfolio_publication_form.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_form.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a profile publisher component which can be used
 * to create a new learning object before publishing it.
 */
class PortfolioPublicationCreator extends PortfolioPublisherComponent
{
	/*
	 * Inherited
	 */
	function as_html()
	{
		$oid = $_GET[PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID];
		if ($oid)
		{
			if ($_GET[PortfolioPublisher :: PARAM_EDIT])
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
		$form->addElement('hidden', PortfolioPublisher :: PARAM_ACTION);
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$form->setDefaults(array ('tool' => $_GET['tool'], PortfolioPublisher :: PARAM_ACTION => $_GET[PortfolioPublisher :: PARAM_ACTION]));
		return $form->asHtml();
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
	 * Gets the editing form
	 */
	private function get_editing_form($objectID)
	{
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $object, 'edit', 'post', $this->get_url(array (PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID => $objectID, PortfolioPublisher :: PARAM_EDIT => 1)));
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
		$out = ($new ? Display :: normal_message(htmlentities(Translation :: get('ObjectCreated')), true) : '');
		$tool = $this->get_parent()->get_parent();
		$object = RepositoryDataManager :: get_instance()->retrieve_learning_object($objectID);

		$pid = $this->pid;
		$publication = null;
		if (isset($pid))
		{
			$publication = PortfolioDataManager :: get_instance()->retrieve_portfolio_publication($pid);
		}

		$form = new PortfolioPublicationForm($object, $this->get_user(),$this->get_url(array(PortfolioPublisher :: PARAM_LEARNING_OBJECT_ID => $object->get_id())));
		if ($form->validate())
		{
			$failures = 0;
			if ($form->create_learning_object_publication())
			{
				$message = 'PortfolioPublished';
			}
			else
			{
				$failures++;
				$message = 'PortfolioNotPublished';
			}
			// TODO: Use a function for this.

			$this->redirect(null, Translation :: get($message), ($failures ? true : false), array(MyPortfolioManager :: PARAM_ACTION => MyPortfolioManager :: ACTION_VIEW));
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
