<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/group_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../group_table/default_group_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../group.class.php';
require_once dirname(__FILE__).'/../../group_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class GroupBrowserTableCellRenderer extends DefaultGroupTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function GroupBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $group)
	{
		if ($column === GroupBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($group);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case Group :: PROPERTY_NAME :
				$title = parent :: render_cell($column, $group);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_group_viewing_url($group)).'" title="'.$title.'">'.$title_short.'</a>';
			case Group :: PROPERTY_DESCRIPTION :
				$description = strip_tags(parent :: render_cell($column, $group));
				if(strlen($description) > 175)
				{
					$description = mb_substr($description,0,170).'&hellip;';
				}
				return $description;
		}
		
		switch($column->get_title())
		{
			case Translation :: get('Users') :
				$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID,$group->get_id());
				$count = $this->browser->count_group_rel_users($condition);
				return $count;
			case Translation :: get('Subgroups') :
				$condition = new EqualityCondition(Group :: PROPERTY_PARENT,$group->get_id()); 
				$count = $this->browser->count_groups($condition);
				return $count;	
		}
		
		return parent :: render_cell($column, $group);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($group)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_group_editing_url($group),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_group_suscribe_user_browser_url($group),
			'label' => Translation :: get('AddUsers'),
			'img' => Theme :: get_common_image_path().'action_subscribe.png',
		);
		
		$condition = new EqualityCondition(GroupRelUser :: PROPERTY_GROUP_ID, $group->get_id());
		$users = $this->browser->retrieve_group_rel_users($condition);
		$visible = ($users->size() > 0);
		
		if($visible)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_group_emptying_url($group),
				'label' => Translation :: get('Truncate'),
				'img' => Theme :: get_common_image_path().'action_recycle_bin.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('TruncateNA'),
				'img' => Theme :: get_common_image_path().'action_recycle_bin_na.png',
			);
		}
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_group_delete_url($group),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_move_group_url($group),
			'label' => Translation :: get('Move'),
			'img' => Theme :: get_common_image_path().'action_move.png'
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>