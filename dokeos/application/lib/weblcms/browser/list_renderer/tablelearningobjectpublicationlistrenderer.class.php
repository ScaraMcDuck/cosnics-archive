<?php
/**
 * @package application.weblcms
 * @subpackage browser.listrenderer
 */
require_once dirname(__FILE__).'/../learningobjectpublicationlistrenderer.class.php';

class TableLearningObjectPublicationListRenderer extends LearningObjectPublicationListRenderer
{
	private $table;

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
				$actions['move'] = get_lang('Move');
			}
			if($browser->is_allowed(DELETE_RIGHT))
			{
				$actions['delete'] = get_lang('Delete');
			}
			$this->table->set_form_actions($actions);
		}
	}

	function set_header($column, $label, $sortable = true)
	{
		return $this->table->set_header($column, $label, $sortable);
	}

	function as_html()
	{
		return $this->table->as_html();
	}
}
?>