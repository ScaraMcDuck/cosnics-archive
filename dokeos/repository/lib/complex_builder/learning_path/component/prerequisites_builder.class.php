<?php

require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once dirname(__FILE__) . '/prerequisites_builder/prerequisites_builder_form.class.php';

class LearningPathBuilderPrerequisitesBuilderComponent extends LearningPathBuilderComponent
{
	function run()
	{
		$cloi_id = Request :: get(LearningPathBuilder :: PARAM_SELECTED_CLOI_ID);
		$parent_cloi = Request :: get(LearningPathBuilder :: PARAM_CLOI_ID);
		
		$menu_trail = $this->get_clo_breadcrumbs();
		$trail = new BreadcrumbTrail(false);
		$trail->merge($menu_trail);
		
		$parameters = array(LearningPathBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), LearningPathBuilder :: PARAM_CLOI_ID => $parent_cloi,
			LearningPathBuilder :: PARAM_SELECTED_CLOI_ID => $cloi_id, 'publish' => Request :: get('publish'));
		
		$trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('BuildPrerequisites')));
		
		if(!$cloi_id)
		{
			$this->display_header($trail);
			$this->display_error_message(Translation :: get('NoObjectSelected'));
			$this->display_footer();
			exit;
		}
		
		$selected_cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($cloi_id);
		$form = new PrerequisitesBuilderForm($this->get_user(), $selected_cloi, $this->get_url($parameters));
		
		if($form->validate())
		{
			$form->build_prerequisites();	
		}
		else 
		{
			$this->display_header($trail);
			$form->display();
			$this->display_footer();
		}
		
	}
}

?>