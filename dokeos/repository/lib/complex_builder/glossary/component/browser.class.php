<?php

require_once dirname(__FILE__) . '/../glossary_builder_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once Path :: get_repository_path() . '/lib/learning_object/glossary/glossary.class.php';

class GlossaryBuilderBrowserComponent extends GlossaryBuilderComponent
{
	function run()
	{
		$glossary = $this->get_root_lo();
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('Repository')));
		$trail->add(new Breadcrumb($this->get_url(array(ComplexBuilder :: PARAM_ROOT_LO => $glossary->get_id())), $glossary->get_title()));
		$this->display_header($trail);
		
		$action_bar = $this->get_action_bar($glossary);
		
		echo '<br />';
		echo $action_bar->as_html();
		echo '<br />';
		$display = LearningObjectDisplay :: factory($glossary);
		echo $display->get_full_html();
		echo $this->get_clo_table_html(false);
		
		$this->display_footer();
	}
	
	function get_action_bar($glossary)
	{
		$pub = Request :: get('publish');
		
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$types = $glossary->get_allowed_types();
		foreach($types as $type)
		{
			$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $glossary->get_id()));
			$action_bar->add_common_action(new ToolbarItem(Translation :: get(DokeosUtilities :: underscores_to_camelcase($type . 'TypeName')), Theme :: get_common_image_path().'learning_object/' . $type . '.png', $url));	
		}
		
		if($pub && $pub != '')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $_SESSION['redirect_url']));
		}

		return $action_bar;
	}
}

?>
