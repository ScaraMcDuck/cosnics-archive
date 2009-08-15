<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/rights_template_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../rights_template_table/default_rights_template_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../rights_template.class.php';
require_once dirname(__FILE__).'/../../rights_template_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RightsTemplateBrowserTableCellRenderer extends DefaultRightsTemplateTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function RightsTemplateBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $rights_template)
	{
		if ($column === RightsTemplateBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($rights_template);
		}
		
		return parent :: render_cell($column, $rights_template);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($rights_template)
	{
		$toolbar_data = array();
		
		$editing_url = $this->browser->get_rights_template_editing_url($rights_template);
		$toolbar_data[] = array(
			'href' => $editing_url,
			'label' => Translation :: get('Edit'),
			'img' => Theme :: get_common_image_path().'action_edit.png',
		);
		
		$deleting_url = $this->browser->get_rights_template_deleting_url($rights_template);
		$toolbar_data[] = array(
			'href' => $deleting_url,
			'label' => Translation :: get('Delete'),
			'img' => Theme :: get_common_image_path().'action_delete.png',
		);
		
		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>