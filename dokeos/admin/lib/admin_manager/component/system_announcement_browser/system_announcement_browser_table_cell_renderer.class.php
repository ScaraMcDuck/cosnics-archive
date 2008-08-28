<?php
/**
 * @package application.lib.profiler.profiler_manager.component.profilepublicationbrowser
 */
require_once dirname(__FILE__).'/system_announcement_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../system_announcement_table/default_system_announcement_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../admin_manager.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class SystemAnnouncementBrowserTableCellRenderer extends DefaultSystemAnnouncementTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param ProfileManagerBrowserComponent $browser
	 */
	function SystemAnnouncementBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $system_announcement)
	{
		if ($column === SystemAnnouncementBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($system_announcement);
		}
		
		// Add special features here
		switch ($column->get_object_property())
		{
			case SystemAnnouncement :: PROPERTY_PUBLISHED:
				return Text :: format_locale_date(Translation :: get('dateFormatShort').', '.Translation :: get('timeNoSecFormat'),$system_announcement->get_published());
				break;
			case SystemAnnouncement :: PROPERTY_LEARNING_OBJECT_ID:
				$title = parent :: render_cell($column, $system_announcement);
				$title_short = $title;
				if(strlen($title_short) > 53)
				{
					$title_short = mb_substr($title_short,0,50).'&hellip;';
				}
				return '<a href="'.htmlentities($this->browser->get_system_announcement_viewing_url($system_announcement)).'" title="'.$title.'">'.$title_short.'</a>';
				break;	
		}
		return parent :: render_cell($column, $system_announcement);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $profile The profile object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($system_announcement)
	{
		$toolbar_data = array();
		
		if ($this->browser->get_user()->is_platform_admin() || $system_announcement->get_publisher() == $this->browser->get_user()->get_id())
		{
			$edit_url = $this->browser->get_system_announcement_editing_url($system_announcement);
			$toolbar_data[] = array(
				'href' => $edit_url,
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_img_path().'action_edit.png'
			);
			
			$delete_url = $this->browser->get_system_announcement_deleting_url($system_announcement);
			$toolbar_data[] = array(
				'href' => $delete_url,
				'label' => Translation :: get('Delete'),
				'confirm' => true,
				'img' => Theme :: get_common_img_path().'action_delete.png'
			);
		}
	
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>