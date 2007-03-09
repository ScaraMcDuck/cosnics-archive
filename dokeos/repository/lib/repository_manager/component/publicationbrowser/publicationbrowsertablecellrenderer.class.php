<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/publicationbrowsertablecolumnmodel.class.php';
require_once dirname(__FILE__).'/../../../publication_table/defaultpublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../learningobject.class.php';
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
				return null;
		$toolbar_data = array();
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_editing_url($learning_object),
			'label' => get_lang('Edit'),
			'img' => $this->browser->get_web_code_path().'img/edit.gif'
		);
		$html = array ();
		if ($url = $this->browser->get_learning_object_recycling_url($learning_object))
		{
			$toolbar_data[] = array(
				'href' => $url,
				'label' => get_lang('Remove'),
				'img' => $this->browser->get_web_code_path().'img/recycle_bin.gif',
				'confirm' => true
			);
		}
		else
		{
			$toolbar_data[] = array(
				'label' => get_lang('Remove'),
				'img' => $this->browser->get_web_code_path().'img/recycle_bin_na.gif'
			);
		}
		if($this->browser->get_number_of_categories() > 1)
		{
			$toolbar_data[] = array(
				'href' => $this->browser->get_learning_object_moving_url($learning_object),
				'label' => get_lang('Move'),
				'img' => $this->browser->get_web_code_path().'img/move.gif'
			);
		}
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_metadata_editing_url($learning_object),
			'label' => get_lang('Metadata'),
			'img' => $this->browser->get_web_code_path().'img/info_small.gif'
		);
		$toolbar_data[] = array(
			'href' => $this->browser->get_learning_object_rights_editing_url($learning_object),
			'label' => get_lang('Rights'),
			'img' => $this->browser->get_web_code_path().'img/group_small.gif'
		);
		return RepositoryUtilities :: build_toolbar($toolbar_data);
	}
}
?>