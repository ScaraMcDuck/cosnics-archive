<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/publication_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../publication_table/default_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../learning_object.class.php';
require_once dirname(__FILE__).'/../../repository_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class PublicationBrowserTableCellRenderer extends DefaultPublicationTableCellRenderer
{
	/**
	 * The repository browser component
	 */
	private $browser;
	/**
	 * Constructor
	 * @param RepositoryManagerBrowserComponent $browser
	 */
	function PublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}
	// Inherited
	function render_cell($column, $learning_object)
	{
		if ($column === PublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($learning_object);
		}

		// Add special features here
		switch ($column->get_object_property())
		{
			case LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE:
				return Text :: format_locale_date(Translation :: get('dateFormatShort').', '.Translation :: get('timeNoSecFormat'),$learning_object->get_publication_date());
		}
		return parent :: render_cell($column, $learning_object);
	}
	/**
	 * Gets the action links to display
	 * @param LearningObject $learning_object The learning object for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($learning_object)
	{
		if (!$learning_object->get_publication_object()->is_latest_version())
		{
			$toolbar_data = array();
			$update_url = $this->browser->get_publication_update_url($learning_object);
			$toolbar_data[] = array(
				'href' => $update_url,
				'label' => Translation :: get('Update'),
				'confirm' => true,
				'img' => Theme :: get_common_image_path().'action_revert.png'
			);
			return DokeosUtilities :: build_toolbar($toolbar_data);
		}
		return '';
	}
}
?>