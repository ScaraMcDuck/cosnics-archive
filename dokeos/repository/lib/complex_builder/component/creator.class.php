<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
require_once dirname(__FILE__) . '/../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . 'lib/repository_data_manager.class.php';

class ComplexBuilderCreatorComponent extends ComplexBuilderComponent
{
	function run()
	{
		$trail = new BreadcrumbTrail();
        
		$object = Request :: get('object');
		$type = Request :: get(ComplexBuilder :: PARAM_TYPE);
		$root_lo = Request :: get(ComplexBuilder :: PARAM_ROOT_LO);
		$cloi_id = Request :: get(ComplexBuilder :: PARAM_CLOI_ID);
		
		$pub = new ComplexRepoViewer($this, $type, true);
		$pub->set_parameter(ComplexBuilder :: PARAM_TYPE, $type);
		$pub->set_parameter(ComplexBuilder :: PARAM_ROOT_LO, $root_lo);
		$pub->set_parameter(ComplexBuilder :: PARAM_CLOI_ID, $cloi_id);
		
		if(!isset($object))
		{	
			$html[] = '<p><a href="' . $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo)) . '"><img src="'.Theme :: get_common_image_path().'action_browser.png" alt="'.Translation :: get('BrowserTitle').'" style="vertical-align:middle;"/> '.Translation :: get('BrowserTitle').'</a></p>';
			$html[] =  $pub->as_html();
		}
		else
		{
			if(!is_array($object))
			{
				$object = array($object);
			}
			
			foreach($object as $obj)
			{
				$rdm = RepositoryDataManager :: get_instance();
				$type = $rdm->determine_learning_object_type($obj);
				
				$cloi = ComplexLearningObjectItem :: factory($type);
				$cloi->set_ref($obj);
				
				$parent = $root_lo;
				if($cloi_id)
				{
					$parent_cloi = $rdm->retrieve_complex_learning_object_item($cloi_id);
					$parent = $parent_cloi->get_ref();
				}
				
				$cloi->set_parent($parent);
				$cloi->set_display_order($rdm->select_next_display_order($parent));
				$cloi->set_user_id($this->get_user_id());
				$cloi->create();
			}
			
			$this->redirect(Translation :: get('QuestionAdded'), false, array('go' => 'build_complex', ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_BROWSE_CLO, ComplexBuilder :: PARAM_ROOT_LO => $root_lo));
		}
		
		$this->display_header($trail);
		echo '<br />' . implode("\n",$html);
		$this->display_footer();
	}
}

?>
