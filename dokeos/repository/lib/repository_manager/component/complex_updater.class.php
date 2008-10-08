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
class RepositoryManagerComplexUpdaterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();

		$cloi_id = $_GET[RepositoryManager :: PARAM_CLOI_ID];
		$root_id = $_GET[RepositoryManager :: PARAM_CLOI_ROOT_ID];
		
		if(!isset($cloi_id) || !isset($root_id))
		{
			$this->display_header($trail);	
			Display :: display_warning_message('Reference is not set');			
			$this->display_footer();
			exit();
		}
		
		$cloi = $this->retrieve_complex_learning_object_item($cloi_id);
		
		$cloi_form = ComplexLearningObjectItemForm :: factory(ComplexLearningObjectItemForm :: TYPE_EDIT, $cloi, 'create_complex', 'post', 
						$this->get_url(array(RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id,
											 RepositoryManager :: PARAM_CLOI_ID => $cloi_id, 'publish' => $_GET['publish'])));		

		if ($cloi_form->validate())
		{ 
			$cloi_form->update_complex_learning_object_item();
			$cloi = $cloi_form->get_complex_learning_object_item();
			$this->redirect(RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, Translation :: get('ObjectUpdated'), 0, false, array(RepositoryManager :: PARAM_CLOI_ID => $cloi->get_parent(),  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
		}
		else
		{
			$this->display_header($trail);
			echo '<p>' . Translation :: get('FillIn') . '</p>';
			$cloi_form->display();
			$this->display_footer();
		}
	}
}
?>