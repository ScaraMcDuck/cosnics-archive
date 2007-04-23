<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/publicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../publication_table/defaultpublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../personal_messenger.class.php';
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
	function render_cell($column, $personal_message)
	{
		if ($column === PublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($personal_message);
		}
		
		// Add special features here
		switch ($column->get_personal_message_property())
		{
			// Exceptions or post-processing go here ...
		}
		return parent :: render_cell($column, $personal_message);
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
		
//		if (!$learning_object->get_publication_object()->is_latest_version())
//		{
//			$update_url = $this->browser->get_publication_update_url($learning_object);
//			$toolbar_data[] = array(
//				'href' => $update_url,
//				'label' => get_lang('Update'),
//				'confirm' => true,
//				'img' => $this->browser->get_web_code_path().'img/revert.gif'
//			);
//		}
//		
//		return RepositoryUtilities :: build_toolbar($toolbar_data);
		return null;
	}
}
?>