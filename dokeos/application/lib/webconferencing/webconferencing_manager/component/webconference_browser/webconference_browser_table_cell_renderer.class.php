<?php
/**
 * @package webconferencing.tables.webconference_table
 */
require_once dirname(__FILE__).'/webconference_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/webconference_table/default_webconference_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../webconference.class.php';
require_once dirname(__FILE__).'/../../webconferencing_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 * @author Stefaan Vanbillemont
 */

class WebconferenceBrowserTableCellRenderer extends DefaultWebconferenceTableCellRenderer
{
	/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function WebconferenceBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $webconference)
	{
		if ($column === WebconferenceBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($webconference);
		}

		return parent :: render_cell($column, $webconference);
	}

	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($webconference)
	{
		$toolbar_data = array();

		if ($this->browser->get_user()->is_platform_admin() || $webconference->get_user_id() == $this->browser->get_user()->get_id())
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_update_webconference_url($webconference),
				'label' => Translation :: get('Edit'),
				'img' => Theme :: get_common_image_path().'action_edit.png'
			);

			$toolbar_data[] = array(
				'href' => $this->browser->get_delete_webconference_url($webconference),
				'label' => Translation :: get('Delete'),
				'img' => Theme :: get_common_image_path().'action_delete.png',
			);
		}

		return DokeosUtilities :: build_toolbar($toolbar_data);
	}
}
?>