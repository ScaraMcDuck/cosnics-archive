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

		if(!isset($ref))
		{
			$this->display_header($trail, false, true, 'repository general');
			Display :: warning_message('Reference is not set');
			$this->display_footer();
		}

		if($parent)
		{
			$type = RepositoryDataManager :: get_instance()->determine_learning_object_type($ref);
			$cloi = ComplexLearningObjectItem :: factory($type);

			$cloi->set_ref($ref);
			$cloi->set_user_id($owner);
			$cloi->set_parent($parent);
			$cloi->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($parent));

			$cloi_form = ComplexLearningObjectItemForm :: factory(ComplexLearningObjectItemForm :: TYPE_CREATE, $cloi, 'create_complex', 'post',
							$this->get_url(array(RepositoryManager :: PARAM_CLOI_REF => $ref,
												 RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id,
												 RepositoryManager :: PARAM_CLOI_ID => $parent, 'publish' => $_GET['publish'])));

			if($cloi_form)
			{
				if ($cloi_form->validate())
				{
					$cloi_form->create_complex_learning_object_item();
					$cloi = $cloi_form->get_complex_learning_object_item();
					$root_id = $root_id?$root_id:$cloi->get_id();
					if($cloi->is_complex()) $id = $cloi->get_ref(); else $id = $cloi->get_parent();
					$this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $id,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
				}
				else
				{
					$this->display_header($trail, false, false, 'repository general');
					echo '<p>' . Translation :: get('FillIn') . '</p>';
					$cloi_form->display();
					$this->display_footer();
				}
			}
			else
			{
				$cloi->create();
				$root_id = $root_id?$root_id:$cloi->get_id();
				if($cloi->is_complex()) $id = $cloi->get_ref(); else $id = $cloi->get_parent();
				$this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $id,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id, 'publish' => $_GET['publish']));
			}
		}
		else
			$this->redirect(Translation :: get('ObjectCreated'), false, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_LEARNING_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $ref,  RepositoryManager :: PARAM_CLOI_ROOT_ID => $ref, 'publish' => $_GET['publish']));
	}
}
?>
