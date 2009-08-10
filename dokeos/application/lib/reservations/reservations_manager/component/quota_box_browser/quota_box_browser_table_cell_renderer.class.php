<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/quota_box_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/quota_box_table/default_quota_box_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../quota_box.class.php';
require_once dirname(__FILE__).'/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class QuotaBoxBrowserTableCellRenderer extends DefaultQuotaBoxTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	protected $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function QuotaBoxBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	
	function render_cell($column, $quota_box)
	{
		if ($column === QuotaBoxBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($quota_box);
		}
		
		return parent :: render_cell($column, $quota_box);
	}
	
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($quota_box)
	{
		$toolbar_data = array();
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_update_quota_box_url($quota_box->get_id()),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_theme_path() . 'action_edit.png'
		);
		
		$toolbar_data[] = array(
				'href' => $this->browser->get_delete_quota_box_url($quota_box->get_id()),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_theme_path() . 'action_delete.png',
				'confirm' => true
		);
		
		return Utilities :: build_toolbar($toolbar_data);
	}
}
?>