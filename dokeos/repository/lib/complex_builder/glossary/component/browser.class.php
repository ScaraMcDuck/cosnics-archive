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
		if($action_bar)
		{
			echo $action_bar->as_html();
			echo '<br />';
		}
		
		$display = LearningObjectDisplay :: factory($this->get_root_lo());
		echo $display->get_full_html();
		
		echo '<br />';
		echo $this->get_creation_links($glossary);
		echo '<div class="clear">&nbsp;</div><br />';
		
		echo $this->get_clo_table_html(false);
		
		$this->display_footer();
	}
}

?>
