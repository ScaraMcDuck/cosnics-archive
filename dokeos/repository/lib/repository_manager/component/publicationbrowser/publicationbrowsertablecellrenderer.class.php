<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/publicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../publication_table/defaultpublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
require_once dirname(__FILE__).'/../../repositorymanager.class.php';
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
		switch ($column->get_learning_object_property())
		{
			case LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE:
				return format_locale_date(get_lang('dateFormatShort').', '.get_lang('timeNoSecFormat'),$learning_object->get_publication_date());
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
		$toolbar_data = array();
		
		if (!$learning_object->get_publication_object()->is_latest_version())
		{
			$update_url = $this->browser->get_publication_update_url($learning_object);
			$toolbar_data[] = array(
				'href' => $update_url,
				'label' => get_lang('Update'),
				'confirm' => true,
				'img' => $this->browser->get_web_code_path().'img/revert.gif'
			);
		}
		
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>