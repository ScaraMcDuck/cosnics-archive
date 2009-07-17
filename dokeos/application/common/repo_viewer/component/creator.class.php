<?php
/**
 * @package application.lib.encyclopedia.repo_viewer
 */
require_once dirname(__FILE__).'/../repo_viewer.class.php';
require_once dirname(__FILE__).'/../repo_viewer_component.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_display.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learning_object_form.class.php';
require_once dirname(__FILE__).'/../../../../common/dokeos_utilities.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to create a new learning object before publishing it.
 */
class RepoViewerCreatorComponent extends RepoViewerComponent
{
	/*
	 * Inherited
	 */
	function as_html($params = array())
	{
		$oid = Request :: get(RepoViewer :: PARAM_EDIT_ID);
		if ($oid)
		{
			//if (Request :: get(RepoViewer :: PARAM_EDIT))
			//{
				return $this->get_editing_form($oid, $params);
			//}
		}
		/*else if (Request :: get(RepoViewer :: PARAM_ID))
		{
			$redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ID => Request :: get(RepoViewer :: PARAM_ID)));
			$this->redirect(null, false, $redirect_params);
		}*/
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
		$form = new FormValidator('selecttype', 'post', $this->get_url($this->get_parameters()));
		$form->addElement('hidden', 'tool');
		$form->addElement('hidden', RepoViewer :: PARAM_ACTION);
		$form->addElement('select', 'type', '', $types);
		$form->addElement('submit', 'submit', Translation :: get('Ok'));
		$form->setDefaults(array (RepoViewer :: PARAM_ACTION => Request :: get(RepoViewer :: PARAM_ACTION)));
		
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
		$default_lo = $this->get_default_learning_object($type);
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $default_lo, 'create', 'post', $this->get_url(array_merge(array ('type' => $type), $this->get_parameters())));
		
		$def = $this->get_creation_defaults();
		if($def)
			$form->setParentDefaults($def);

		return $this->handle_form($form, 0);
	}
	
	/**
	 * Gets the editing form
	 */
	private function get_editing_form($learning_object_id, $params = array())
	{
		$learning_object = RepositoryDataManager :: get_instance()->retrieve_learning_object($learning_object_id);
		$form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $learning_object, 'edit', 'post', $this->get_url(array_merge($this->get_parameters(), array_merge($params,array (RepoViewer :: PARAM_EDIT_ID => $learning_object_id))))); //, RepoViewer :: PARAM_EDIT => 1)))));
		return $this->handle_form($form, 1);
	}
	
	/*
	 * Handles the displaying and validating of a create/edit learning object form
	 */
	private function handle_form($form, $edit = 0) {
		if ($form->validate())
		{
			if ($edit)
			{
				$form->update_learning_object();
				$learning_object = $form->get_learning_object();
			}
			else
				$learning_object = $form->create_learning_object();
			//$redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ID => $learning_object->get_id(), RepoViewer :: PARAM_ACTION => 'publicationcreator', RepoViewer :: PARAM_EDIT => $edit));
			//$redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ID => $learning_object->get_id(), RepoViewer :: PARAM_EDIT => $edit));
			if(!is_array($learning_object) && $learning_object->is_complex_learning_object() && $this->redirect_complex($learning_object->get_type()))
			{
				$redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ID => $learning_object->get_id()));
				$_SESSION['redirect_url'] = $this->get_url($redirect_params);
				header('Location: index_repository_manager.php?go=build_complex&publish=1&root_lo=' . $learning_object->get_id());
			}
			else
			{
				if(is_array($learning_object))
				{
					$ids = array();
					foreach($learning_object as $lo)
						$ids[] = $lo->get_id();
				}
				else
				{
					$ids = $learning_object->get_id();
				}
				
				$redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ID => $ids));
				$this->redirect(null, false, $redirect_params);
			}
		}
		else
		{
			return $form->toHtml();
		}
	}
}
?>