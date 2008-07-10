<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../complex_learning_object_item_form.class.php';
require_once dirname(__FILE__).'/../../repository_data_manager.class.php';

/**
 * Repository manager component which gives the user the possibility to create a
 * new complex learning object item in his repository. 
 */
class RepositoryManagerComplexCreatorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();

		$owner = $this->get_user()->get_id();
		$ref = $_GET[RepositoryManager :: PARAM_CLOI_REF];
		$parent = $_GET[RepositoryManager :: PARAM_CLOI_ID];
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		$is_complex = $_GET[RepositoryManager :: PARAM_CLOI_COMPLEX_REF];
		
		if(!isset($ref))
		{
			$this->display_header($trail);	
			Display :: display_warning_message('Reference is not set');			
			$this->display_footer();
		}

		if($is_complex)
		{
			$item = $this->retrieve_complex_learning_object_item($ref);
			$typeref = $item->get_ref();
		}
		else
			$typeref = $ref;
		
		$type = RepositoryDataManager :: get_instance()->determine_learning_object_type($typeref);
		$cloi = ComplexLearningObjectItem :: factory($type);

		$cloi->set_ref($ref);
		$cloi->set_complex_ref($is_complex?$is_complex:0);
		$cloi->set_user_id($owner);
		$cloi->set_parent($parent?$parent:0);
		
		if(count($cloi->get_allowed_types()) > 0 && isset($root_id) && isset($parent) && !isset($is_complex))
		{
			$cloi->set_parent(0);
			$cloi->create();
			$ref = $cloi->get_id();
			
			$cloi = ComplexLearningObjectItem :: factory($type);
			$cloi->set_ref($ref);
			$cloi->set_complex_ref(1);
			$is_complex = 1;
			$cloi->set_parent($parent?$parent:0);
			$cloi->set_user_id($owner);
		}
	
		$cloi_form = ComplexLearningObjectItemForm :: factory(ComplexLearningObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post', 
						$this->get_url(array(RepositoryManager :: PARAM_CLOI_REF => $ref, 
											 RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id,
											 RepositoryManager :: PARAM_CLOI_ID => $parent,
											 RepositoryManager :: PARAM_CLOI_COMPLEX_REF => $is_complex)));		
		
		if($cloi_form)
		{
			if ($cloi_form->validate())
			{ 
				$cloi_form->create_complex_learning_object_item();
				$cloi = $cloi_form->get_complex_learning_object_item();
				$root_id = $root_id?$root_id:$cloi->get_id();
				if($cloi->is_complex()) $id = $cloi->get_id(); else $id = $cloi->get_parent();
				$this->redirect(RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, Translation :: get('ObjectCreated'), 0, false, array(RepositoryManager :: PARAM_CLOI_ID => $id,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id));
			}
			else
			{
				$this->display_header($trail);
				echo '<p>' . Translation :: get('FillIn') . '</p>';
				$cloi_form->display();
				$this->display_footer();
			}
		}
		else
		{
			$cloi->create();
			$root_id = $root_id?$root_id:$cloi->get_id();
			if($cloi->is_complex()) $id = $cloi->get_id(); else $id = $cloi->get_parent();
			$this->redirect(RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, Translation :: get('ObjectCreated'), 0, false, array(RepositoryManager :: PARAM_CLOI_ID => $id,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id));
		}
	}
}
?>
