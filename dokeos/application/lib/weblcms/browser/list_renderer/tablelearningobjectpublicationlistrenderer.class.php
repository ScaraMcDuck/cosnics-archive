<?php
/**
 * $Id$
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';
/**
 * Renderer to display a sortable table with learning object publications.
 */
class TableLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	/**
	 * The table with all learning object publications to be displayed
	 */
	protected $table;
	/**
	 * Create a new table renderer
	 * @param PublicationBrowser $browser The browser to associate this table
	 * renderer with.
	 */
	function TableLearningObjectPublicationListRenderer($browser)
	{
		parent :: __construct($browser);
		// TODO: Assign a dynamic table name.
		$name = 'pubtbl';
		$this->table = new SortableTable($name, array($browser, 'get_publication_count'), array($browser, 'get_publications'));
		$this->table->set_additional_parameters($browser->get_parameters());
		if($browser->is_allowed(EDIT_RIGHT) || $browser->is_allowed(DELETE_RIGHT))
		{
			if($browser->is_allowed(EDIT_RIGHT))
			{
				$actions[RepositoryTool::ACTION_MOVE_SELECTED_TO_CATEGORY] = Translation :: get_lang('Move');
			}
			if($browser->is_allowed(DELETE_RIGHT))
			{
				$actions[RepositoryTool::ACTION_DELETE_SELECTED] = Translation :: get_lang('Delete');
			}
			$this->table->set_form_actions($actions,'id',RepositoryTool::PARAM_ACTION);
		}
	}
	/**
	 * Sets a header of the table
	 * @see SortableTable::set_header()
	 */
	function set_header($column, $label, $sortable = true)
	{
		return $this->table->set_header($column, $label, $sortable);
	}
	/**
	 * Returns the HTML output of this renderer.
	 * @return string The HTML output
	 */
	function as_html()
	{
		return $this->table->as_html();
	}
}
?>