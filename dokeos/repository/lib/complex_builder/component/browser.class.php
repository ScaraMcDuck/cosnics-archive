<?php

require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';

class ComplexBuilderBrowserComponent extends ComplexBuilderComponent
{
	function run()
	{
		$menu_trail = $this->get_clo_breadcrumbs();
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('Repository')));
		$trail->merge($menu_trail);
		
		if($this->get_cloi())
			$lo = $this->rdm->retrieve_learning_object($this->get_cloi()->get_ref());
		else
			$lo = $this->get_root_lo();
		
		$this->display_header($trail);
		$action_bar = $this->get_action_bar($lo);
		
		echo '<br />';
		echo $action_bar->as_html();
		echo '<br />';
		
		$display = LearningObjectDisplay :: factory($this->get_root_lo());
		echo $display->get_full_html();
		
		echo '<div style="width: 18%; overflow: auto; float: left;">';
		echo $this->get_clo_menu();
		echo '</div><div style="width: 80%; float: right;">';
		echo $this->get_clo_table_html();
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		
		$this->display_footer();
	}
}

?>
