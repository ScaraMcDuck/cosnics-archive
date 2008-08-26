<?php
/**
 * $Id: deleter.class.php 15420 2008-05-26 17:34:32Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerImporterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$clo_id = $_GET[RepositoryManager :: PARAM_CLOI_ID]; 
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('LearningObjectImport')));
		
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
		
		$import_form = new FormValidator('import', 'post');
						
		$import_form->addElement('file', 'file', Translation :: get('FileName'));
		$import_form->addElement('submit', 'learning_object_import', Translation :: get('Ok'));
		
		if ($import_form->validate())
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
			$this->display_header($trail);
			$quotamanager = new QuotaManager($this->get_user());
			if ( $quotamanager->get_available_database_space() <= 0)
			{
				Display :: display_warning_message(htmlentities(Translation :: get('MaxNumberOfLearningObjectsReached')));
			}
			else
			{
				$import_form->display();
			}
			$this->display_footer();
		}
	}
}
?>