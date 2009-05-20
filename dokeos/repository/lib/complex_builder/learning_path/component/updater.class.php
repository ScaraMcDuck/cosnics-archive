<?php

require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class LearningPathBuilderUpdaterComponent extends LearningPathBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();

		$root_lo = Request :: get(LearningPathBuilder :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(LearningPathBuilder :: PARAM_SELECTED_CLOI_ID);
		$parent_cloi = Request :: get(LearningPathBuilder :: PARAM_CLOI_ID);
		
		$parameters = array(LearningPathBuilder :: PARAM_ROOT_LO => $root_lo, LearningPathBuilder :: PARAM_CLOI_ID => $parent_cloi, 
			LearningPathBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'));
		
		$rdm = RepositoryDataManager :: get_instance();
		$cloi = $rdm->retrieve_complex_learning_object_item($cloi_id);
		$lo = $rdm->retrieve_learning_object($cloi->get_ref());
		
		$type = $lo->get_type();
		
		$cloi_form = ComplexLearningObjectItemForm :: factory_with_type(ComplexLearningObjectItemForm :: TYPE_CREATE, $type, $cloi, 'create_complex', 'post', $this->get_url());
		
		if($cloi_form)
		{
			$elements = $cloi_form->get_elements();
			$defaults = $cloi_form->get_default_values();
		}
		
		if($lo->get_type() == 'learning_path_item')
		{
			$item_lo = $lo;
			$lo = $rdm->retrieve_learning_object($lo->get_reference());
		}
		
		$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $lo, 'edit', 'post', $this->get_url($parameters), null, $elements);
		$lo_form->setDefaults($defaults);
			
		if ($lo_form->validate())
		{
			$lo_form->update_learning_object();
			
			if($lo_form->is_version())
			{	
				$new_id = $lo->get_latest_version()->get_id();
				if($item_lo)
				{
					$item_lo->set_reference($new_id);	
					$item_lo->update();
				}
				else
				{
					$cloi->set_ref($new_id);
				}
			}
			
			if($cloi_form)
				$cloi_form->update_cloi_from_values($lo_form->exportValues());
			else
				$cloi->update();

			$parameters[LearningPathBuilder :: PARAM_SELECTED_CLOI_ID] = null;
				
			$this->redirect(Translation :: get('LearningObjectUpdated'), false, 
					array_merge($parameters, array(
						'go' => 'build_complex',
						LearningPathBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_BROWSE_CLO,
						'publish' => Request :: get('publish')
					)));
		}
		else
		{
			$this->display_header(new BreadCrumbTrail(), 'repository learnpath builder');
			echo $lo_form->toHTML();
			$this->display_footer();
		}
				
	}
}

?>