<?php

require_once dirname(__FILE__) . '/../portfolio_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path() . 'lib/complex_learning_object_item_form.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_form.class.php';

class PortfolioBuilderUpdaterComponent extends PortfolioBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();

		$root_lo = Request :: get(PortfolioBuilder :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(PortfolioBuilder :: PARAM_SELECTED_CLOI_ID);
		$parent_cloi = Request :: get(PortfolioBuilder :: PARAM_CLOI_ID);

		$parameters = array(PortfolioBuilder :: PARAM_ROOT_LO => $root_lo, PortfolioBuilder :: PARAM_CLOI_ID => $parent_cloi,
			PortfolioBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'));

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

		if($lo->get_type() == 'portfolio_item')
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

			$parameters[PortfolioBuilder :: PARAM_SELECTED_CLOI_ID] = null;

			$this->redirect(Translation :: get('LearningObjectUpdated'), false,
					array_merge($parameters, array(
						PortfolioBuilder :: PARAM_BUILDER_ACTION => PortfolioBuilder :: ACTION_BROWSE_CLO,
						'publish' => Request :: get('publish')
					)));
		}
		else
		{
			$trail = new BreadcrumbTrail();
			$trail->add_help('repository learnpath builder');
			$this->display_header($trail);
			echo $lo_form->toHTML();
			$this->display_footer();
		}

	}
}

?>