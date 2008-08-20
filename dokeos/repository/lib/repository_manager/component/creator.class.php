<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_form.class.php';
require_once dirname(__FILE__).'/../../abstract_learning_object.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';
require_once dirname(__FILE__).'/../../import/learning_object_import.class.php';
require_once dirname(__FILE__).'/../../quota_manager.class.php';
//require_once dirname(__FILE__).'/csv_creator.class.php';
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
		$trail = new BreadcrumbTrail();
		$clo_id= $_GET[RepositoryManager :: PARAM_CLOI_ID]; 
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		
		$type_options = array ();
		$type_options[''] = '&nbsp;';
		$extra_params = array();
		
		if(isset($clo_id) && isset($root_id))
		{
			$clo = $this->retrieve_learning_object($clo_id);
			$types = $clo->get_allowed_types();
			foreach($types as $type)
			{
				$type_options[$type] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
			}
			
			$extra_params = array(RepositoryManager :: PARAM_CLOI_ID => $clo_id, 
								  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id);
			$extra = '<a href="' . $this->get_add_existing_learning_object_url($root_id, $clo_id) . '">' . Translation :: get('AddExistingLearningObject') . '</a><br /><br />';
			
			$root = $this->retrieve_learning_object($root_id);
			$trail->add(new Breadcrumb($this->get_link(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $root_id)), $root->get_title()));
			$trail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $clo_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id)), Translation :: get('ViewComplexLearningObject')));
		}
		else
		{
			foreach ($this->get_learning_object_types(true) as $type)
			{
				$type_options[$type] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
			}
		}
		
		$type_form = new FormValidator('create_type', 'post', $this->get_url($extra_params));
		
		asort($type_options);
		$type_form->addElement('select', RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE, Translation :: get('CreateANew'), $type_options, array('class' => 'learning-object-creation-type'));
		$type_form->addElement('submit', 'submit', Translation :: get('Ok'));

		$import_form = new FormValidator('import_csv', 'post', $this->get_url($extra_params));
		$import_form->addElement('html', '<br /><br /><br />');
		$import_form->addElement('static', 'info', '<b> Importeer hier</b>');
		$import_form->addElement('html', '<br /><br />');
						
		$import_form->addElement('file', 'file', Translation :: get('FileName'));
		$import_form->addElement('submit', 'course_import', Translation :: get('Ok'));

		$type = ($type_form->validate() ? $type_form->exportValue(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE) : $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE]);

		if ($type)
		{
			$category = $_GET[RepositoryManager :: PARAM_CATEGORY_ID];
			$object = new AbstractLearningObject($type, $this->get_user_id(), $category);
			$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_CREATE, $object, 'create', 'post', $this->get_url(array_merge($extra_params,array(RepositoryManager :: PARAM_LEARNING_OBJECT_TYPE => $type))), null);
			
			if ($lo_form->validate())
			{
				$object = $lo_form->create_learning_object();

				if($object->is_complex_learning_object() || count($extra_params) == 2)
				{
					$params = array_merge(array(RepositoryManager :: PARAM_CLOI_REF => $object->get_id()), $extra_params);
					$this->redirect(RepositoryManager :: ACTION_CREATE_COMPLEX_LEARNING_OBJECTS, null, 0, false, $params);
				}
				else 
					$this->redirect(RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS, Translation :: get('ObjectCreated'), $object->get_parent_id());
			}
			else
			{
				$trail->add(new Breadcrumb($this->get_url(), Translation :: get(LearningObject :: type_to_class($type).'CreationFormTitle')));
				$this->display_header($trail);
				$lo_form->display();
				$this->display_footer();
			}
		}

		else if ($import_form->validate())
		{
			$file = $_FILES['file']['tmp_name'];
			$path_parts = pathinfo($_FILES['file']['name']);
			$extension = $path_parts['extension'];
			$extension = $extension == 'zip' ? 'dlof' : $extension;
			
			if(LearningObjectImport :: type_supported($extension))
			{
				$importer = LearningObjectImport :: factory($extension);
				$lo = $importer->import_learning_object($file, $this->get_parent(), $this->get_user(), $_FILES['file']['name']);
				if(count($extra_params) == 2)
				{
					$params = array_merge(array(RepositoryManager :: PARAM_CLOI_REF => $lo->get_id()), $extra_params);
					$this->redirect(RepositoryManager :: ACTION_CREATE_COMPLEX_LEARNING_OBJECTS, null, 0, false, $params);
				}
				else
					$this->redirect(RepositoryManager :: ACTION_VIEW_LEARNING_OBJECTS, Translation :: get('ObjectImported'), 0, false, array(RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $lo->get_id()));
			}
			else
			{
				$this->display_header($trail);	
				Display :: display_warning_message(Translation :: get('FileTypeNotSupported'));			
				$this->display_footer();
			}
		}
		else
		{
			if($extra)
				$trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddLearningObject')));
			else
				$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Create')));
				
			$this->display_header($trail);
			echo $extra;
			$quotamanager = new QuotaManager($this->get_user());
			if ( $quotamanager->get_available_database_space() <= 0)
			{
				Display :: display_warning_message(htmlentities(Translation :: get('MaxNumberOfLearningObjectsReached')));
			}
			else
			{
				$renderer = clone $type_form->defaultRenderer();
				$renderer->setElementTemplate('{label} {element} ');
				$type_form->accept($renderer);
				echo $renderer->toHTML();
				$import_form->accept($renderer);
				echo $renderer->toHTML();
			}
			$this->display_footer();
		}
	}
}
?>
