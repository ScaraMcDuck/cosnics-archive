<?php

require_once dirname(__FILE__) . '/../learning_path_builder_component.class.php';
require_once Path :: get_library_path() . '/html/action_bar/action_bar_renderer.class.php';
require_once dirname(__FILE__) . '/browser/learning_path_browser_table_cell_renderer.class.php';

class LearningPathBuilderBrowserComponent extends LearningPathBuilderComponent
{
	function run()
	{
		$menu_trail = $this->get_clo_breadcrumbs();
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(array('builder_action' => null, RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS)), Translation :: get('Repository')));
		$trail->merge($menu_trail);
		
		if($this->get_cloi())
			$lo = RepositoryDataManager :: get_instance()->retrieve_learning_object($this->get_cloi()->get_ref());
		else
			$lo = $this->get_root_lo();
		
		$this->display_header($trail);
		$action_bar = $this->get_action_bar($lo);
		
		echo '<br />';
		if($action_bar)
		{
			echo $action_bar->as_html();
			echo '<br />';
		}
		
		$display = LearningObjectDisplay :: factory($this->get_root_lo());
		echo $display->get_full_html();
		
		echo '<br />';
		$types = array('learning_path', 'announcement', 'assessment', 'blog_item', 'calendar_event', 'description', 'document', 'forum', 'glossary', 'link', 'note', 'wiki');
		echo $this->get_creation_links($lo, $types);
		echo '<div class="clear">&nbsp;</div><br />';
		
		echo '<div style="width: 18%; overflow: auto; float: left;">';
		echo $this->get_clo_menu();
		echo '</div><div style="width: 80%; float: right;">';
		echo $this->get_clo_table_html(false, null, new LearningPathBrowserTableCellRenderer($this->get_parent(), $this->get_clo_table_condition()));
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		
		$this->display_footer();
	}
	
	function get_action_bar()
	{
		$pub = Request :: get('publish');
		
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
		
		/*$type = 'learning_path';
		$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get(DokeosUtilities :: underscores_to_camelcase($type . 'TypeName')), Theme :: get_common_image_path().'learning_object/' . $type . '.png', $url));
		
		$types = array('announcement', 'assessment', 'blog_item', 'calendar_event', 'description', 'document', 'forum', 'glossary', 'link', 'note', 'wiki');
		foreach($types as $type)
		{
			$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_LP_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get(DokeosUtilities :: underscores_to_camelcase($type . 'TypeName')), Theme :: get_common_image_path().'learning_object/' . $type . '.png', $url));	
		}*/
		
		if($pub && $pub != '')
		{
			$action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path().'action_publish.png', $_SESSION['redirect_url']));
			return $action_bar;
		}
	}
	
	function get_creation_links($lo, $types = array())
	{
		$html[] = '<div class="category_form"><div id="learning_object_selection">';
		
		if(count($types) == 0)
			$types = $lo->get_allowed_types();
			
		foreach($types as $type)
		{
			if($type == 'learning_path')
			{
				$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
			}
			else
			{
				$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_LP_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
			}
			
			$html[] = '<a href="'. $url .'"><div class="create_complex_block" style="background-image: url(' . Theme :: get_common_image_path() . 'learning_object/big/' . $type . '.png);">';
			$html[] = Translation :: get(LearningObject :: type_to_class($type).'TypeName');
			$html[] = '<div class="clear">&nbsp;</div>';
			$html[] = '</div></a>';
		}
		
		$html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
		$html[] = '</div>';
		$html[] = '<div class="clear">&nbsp;</div>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
}

?>
