<?php
/**
 * @package application.lib.encyclopedia.publisher
 */
require_once dirname(__FILE__).'/../publisher.class.php';
require_once dirname(__FILE__).'/../publisher_component.class.php';
require_once dirname(__FILE__).'/publication_candidate_table/publication_candidate_table.class.php';
/**
 * This class represents a encyclopedia publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PublisherBrowserComponent extends PublisherComponent
{
	private $browser_actions;
	
	function PublisherBrowserComponent($parent)
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
		
		$table = new PublicationCandidateTable($this->get_user(), $this->get_types(), $this->get_query(), $actions);
		return $table->as_html();
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
	
	function get_default_browser_actions()
	{
		$browser_actions = array();
		
		$browser_actions[] = array(
			'href' => $this->get_url(array_merge($this->get_extra_parameters(), array (Publisher :: PARAM_ACTION => 'publicationcreator', Publisher :: PARAM_ID => '__ID__')),false),
			'img' => Theme :: get_common_img_path().'action_publish.png',
			'label' => Translation :: get('Publish')
		);
		
		$browser_actions[] = array(
			'href' => $this->get_url(array_merge($this->get_extra_parameters(), array (Publisher :: PARAM_ACTION => 'publicationcreator', Publisher :: PARAM_ID => '__ID__', Publisher :: PARAM_EDIT => 1))),
			'img' => Theme :: get_common_img_path().'action_editpublish.png',
			'label' => Translation :: get('EditAndPublish')
		);
		
		return $browser_actions;
	}
}
?>