<?php
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
	}
	
	function set_header($column, $label, $sortable = true)
	{
		return $this->table->set_header($column, $label, $sortable);
	}
	
	function render()
	{
		return $this->table->as_html();
	}
}
?>