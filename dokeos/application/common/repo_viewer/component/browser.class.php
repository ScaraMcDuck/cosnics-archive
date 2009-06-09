<?php
/**
 * @package application.lib.encyclopedia.repo_viewer
 */
require_once dirname(__FILE__).'/../repo_viewer.class.php';
require_once dirname(__FILE__).'/../repo_viewer_component.class.php';
require_once dirname(__FILE__).'/learning_object_table/learning_object_table.class.php';
require_once Path :: get_repository_path() . 'lib/learning_object_category_menu.class.php';
//require_once Path :: get_repository_path() . 'lib/forms/repository_filter_form.class.php';
/**
 * This class represents a encyclopedia repo_viewer component which can be used
 * to browse through the possible learning objects to publish.
 */
class RepoViewerBrowserComponent extends RepoViewerComponent
{
	private $browser_actions;
	
	function RepoViewerBrowserComponent($parent)
	{
		parent :: __construct($parent);
		$this->set_browser_actions($this->get_default_browser_actions());
	}
	/*
	 * Inherited
	 */
	function as_html()
	{
		$actions = $this->get_browser_actions();
		foreach($actions as $key => $action)
		{
			$actions[$key]['href'] = str_replace('__ID__', '%d', $action['href']);
		}
		
		if($this->get_maximum_select() > RepoViewer :: SELECT_SINGLE)
			$html[] = '<b>' . sprintf(Translation :: get('SelectMaximumLO'), $this->get_maximum_select()) . '</b><br />';
		
		$menu = $this->get_menu();
		
		$html[] = '<br /><div style="width: 15%; overflow: auto; float:left">';
		$html[] = $menu->render_as_tree();
		$table = new LearningObjectTable($this, $this->get_user(), $this->get_types(), $this->get_query(), $actions);        
		$html[] = '</div><div style="width: 83%; float: right;">' . $table->as_html() . '</div>';
		$html[] = '<div class="clear">&nbsp;</div>';
		return implode("\n", $html);
	}

	/**
	 * Returns the search query.
	 * @return string|null The query, or null if none.
	 */
	protected function get_query()
	{
		return null;
	}
	
	function get_browser_actions()
	{
		return $this->browser_actions;
	}
	
	function set_browser_actions($browser_actions)
	{
		$this->browser_actions = $browser_actions;
	}
	
	function get_menu()
	{
		$url = $_SERVER['REQUEST_URI'] . '&category=%s';
		$menu = new LearningObjectCategoryMenu($this->get_user_id(), Request :: get('category')?Request :: get('category'):0,$url);
		return $menu;
	}
	
	function get_default_browser_actions()
	{
		$browser_actions = array();
		
		$browser_actions[] = array(
			'href' => $this->get_url(array_merge($this->get_parameters(), array (RepoViewer :: PARAM_ACTION => 'publicationcreator', RepoViewer :: PARAM_ID => '__ID__')),false),
			'img' => Theme :: get_common_image_path().'action_publish.png',
			'label' => Translation :: get('Publish')
		);
		
		$browser_actions[] = array(
			'href' => $this->get_url(array_merge($this->get_parameters(), array (RepoViewer :: PARAM_ACTION => 'creator', RepoViewer :: PARAM_EDIT_ID => '__ID__'))), //, RepoViewer :: PARAM_EDIT => 1))),
			'img' => Theme :: get_common_image_path().'action_editpublish.png',
			'label' => Translation :: get('EditAndPublish')
		);
		
		return $browser_actions;
	}
}
?>