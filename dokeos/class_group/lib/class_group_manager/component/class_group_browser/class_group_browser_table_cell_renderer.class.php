<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/class_group_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../class_group_table/default_class_group_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../class_group.class.php';
require_once dirname(__FILE__).'/../../class_group_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ClassGroupBrowserTableCellRenderer extends DefaultClassGroupTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function ClassGroupBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $classgroup)
	{
		if ($column === ClassGroupBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($classgroup);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			// Exceptions that need post-processing go here ...
			case ClassGroup :: PROPERTY_NAME :
				$title = parent :: render_cell($column, $classgroup);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_classgroup_viewing_url($classgroup)).'" title="'.$title.'">'.$title_short.'</a>';
			case ClassGroup :: PROPERTY_DESCRIPTION :
				$description = strip_tags(parent :: render_cell($column, $classgroup));
				if(strlen($description) > 175)
				{
					$description = mb_substr($description,0,170).'&hellip;';
				}
				return $description;
		}
		return parent :: render_cell($column, $classgroup);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($classgroup)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_classgroup_editing_url($classgroup),
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_img_path().'action_edit.png'
		);
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_classgroup_rel_user_subscribing_url($classgroup),
			'label' => Translation :: get('AddUsers'),
			'img' => Theme :: get_common_img_path().'action_subscribe.png',
		);
		
		$condition = new EqualityCondition(ClassGroupRelUser :: PROPERTY_CLASSGROUP_ID, $classgroup->get_id());
		$users = $this->browser->retrieve_classgroup_rel_users($condition);
		$visible = ($users->size() > 0);
		
		if($visible)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_classgroup_emptying_url($classgroup),
				'label' => Translation :: get('Truncate'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin.png',
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => Translation :: get('TruncateNA'),
				'img' => Theme :: get_common_img_path().'action_recycle_bin_na.png',
			);
		}
		
		$toolbar_data[] = array(
			'href' => $this->browser->get_classgroup_delete_url($classgroup),
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_img_path().'action_delete.png'
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>