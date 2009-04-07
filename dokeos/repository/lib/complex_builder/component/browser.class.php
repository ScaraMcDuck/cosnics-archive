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
		
		$this->display_header($trail);
		$action_bar = $this->get_action_bar($assessment);
		
		echo '<br />';
		echo $action_bar->as_html();
		echo '<br />';
		
		echo '<div style="width: 18%; overflow: auto; float: left;">';
		echo $this->get_clo_menu();
		echo '</div><div style="width: 80%; float: right;">';
		echo $this->get_clo_table_html();
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$pub = Request :: get('publish');
		$lo = $this->get_root_lo();
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_ROOT_LO => $lo->get_id(), ComplexBuilder :: PARAM_CLOI_ID => Request :: get(ComplexBuilder :: PARAM_CLOI_ID)));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Create'), Theme :: get_common_image_path().'action_create.png', $url));	
		
		if($pub && $pub != '')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $_SESSION['redirect_url']));
		}

		return $action_bar;
	}
}

?>
