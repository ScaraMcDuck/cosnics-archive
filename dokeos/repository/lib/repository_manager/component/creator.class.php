<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectform.class.php';
require_once dirname(__FILE__).'/../../abstractlearningobject.class.php';
require_once dirname(__FILE__).'/../../repositorydatamanager.class.php';
/**
 * Repository manager component which gives the user the possibility to create a
 * new learning object in his repository. When no type is passed to this
 * component, the user will see a dropdown list in which a learning object type
 * can be selected. Afterwards, the form to create the actual learning object
 * will be displayed.
 */
class RepositoryManagerCreatorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$type_form = new FormValidator('create_type', 'post', $this->get_url());
		$type_options = array ();
		$type_options[''] = '&nbsp;';
		foreach ($this->get_learning_object_types(true) as $type)
		{
			$type_options[$type] = get_lang(LearningObject :: type_to_class($type).'TypeName');
		}
		asort($type_options);
		$type_form->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, get_lang('CreateANew'), $type_options, array('class' => 'learning-object-creation-type'));
		$type_form->addElement('submit', 'submit', get_lang('Ok'));
		$type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE) : $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE]);
		if ($type)
		{
			$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
			$object = new AbstractLearningObject($type, $this->get_user_id(), $category);
			$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE => $type)));
			if ($lo_form->validate())
			{
				$object = $lo_form->create_learning_object();
				$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, get_lang('ObjectCreated'), $object->get_parent_id());
			}
			else
			{
				$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang(LearningObject :: type_to_class($type).'CreationFormTitle')));
				$this->display_header($breadcrumbs);
				$lo_form->display();
				$this->display_footer();
			}
		}
		else
		{
			$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('Create')));
			$this->display_header($breadcrumbs);
			$quotamanager = new QuotaManager($this->get_user());
			if ( $quotamanager->get_available_database_space() <= 0)
			{
				Display :: display_warning_message(htmlentities(get_lang('MaxNumberOfLearningObjectsReached')));
			}
			else
			{
				$renderer = clone $type_form->defaultRenderer();
				$renderer->setElementTemplate('{label} {element} ');
				$type_form->accept($renderer);
				echo $renderer->toHTML();
			}
			$this->display_footer();
		}
	}
}
?>