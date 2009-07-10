<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
require_once dirname(__FILE__) . '/../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class ComplexBuilderUpdaterComponent extends ComplexBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail(false);

		$root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID);
		$parent_cloi = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);

		$parameters = array(ComplexBuilder :: PARAM_ROOT_LO => $root_lo, ComplexBuilder :: PARAM_CLOI_ID => $parent_cloi,
			ComplexBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'));

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

		$lo_form = LearningObjectForm :: factory(LearningObjectForm :: TYPE_EDIT, $lo, 'edit', 'post', $this->get_url($parameters), null, $elements);
		$lo_form->setDefaults($defaults);

		if ($lo_form->validate())
		{
			$lo_form->update_learning_object();

			if($lo_form->is_version())
			{
				$old_id = $cloi->get_ref();
				$new_id = $lo->get_latest_version()->get_id();
				$cloi->set_ref($new_id);
				
				$children = $rdm->retrieve_complex_learning_object_items(new EqualityCondition(ComplexLearningObjectItem :: PROPERTY_PARENT, $old_id));
				while($child = $children->next_result())
				{
					$child->set_parent($new_id);
					$child->update();
				}
			}

			if($cloi_form)
				$cloi_form->update_cloi_from_values($lo_form->exportValues());
			else
				$cloi->update();

			$parameters[ComplexBuilder :: PARAM_SELECTED_CLOI_ID] = null;

			$this->redirect(Translation :: get('LearningObjectUpdated'), false,
					array_merge($parameters, array(
						'go' => 'build_complex',
						ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO,
						'publish' => Request :: get('publish')
					)));
		}
		else
		{
			$trail = new BreadcrumbTrail(false);
			$trail->add_help('repository builder');
			
			$trail->add(new BreadCrumb($this->get_url(array('builder_action' => null, 'root_lo' => $root_lo, 'cid' => Request :: get('cid'), 'publish' => Request :: get('publish'))), RepositoryDataManager :: get_instance()->retrieve_learning_object($root_lo)->get_title()));
        	$trail->add(new BreadCrumb($this->get_url(array('builder_action' => 'update_cloi', 'root_lo' => $root_lo, 'selected_cloi' => $cloi_id, 'cid' => Request :: get('cid'), 'publish' => Request :: get('publish'))), Translation :: get('Update')));
			
			$this->display_header($trail);
			echo $lo_form->toHTML();
			$this->display_footer();
		}

	}
}

?>