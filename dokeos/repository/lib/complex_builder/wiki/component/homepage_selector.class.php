<?php

require_once dirname(__FILE__) . '/../wiki_builder_component.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class WikiBuilderHomepageSelectorComponent extends WikiBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();

		$root = $this->get_root_lo();
		$cloi = RepositoryDataManager :: get_instance()->retrieve_complex_learning_object_item(Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID));
		
		$cloi->set_is_homepage(1);
		$cloi->update();
		
		$this->redirect(Translation :: get('HomepageSelected'), false, 
			array(
				ComplexBuilder :: PARAM_ROOT_LO => $root->get_id(),
				ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO,
				'publish' => Request :: get('publish')
			)
		);
				
	}
}

?>