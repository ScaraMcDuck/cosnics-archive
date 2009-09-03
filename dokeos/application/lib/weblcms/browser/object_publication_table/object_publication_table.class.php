<?php
/**
 * @package application.weblcms.tool.assessment.component.assessment_publication_table
 */
require_once dirname(__FILE__).'/object_publication_table_data_provider.class.php';
require_once dirname(__FILE__).'/object_publication_table_column_model.class.php';
require_once dirname(__FILE__).'/object_publication_table_cell_renderer.class.php';
require_once Path::get_library_path() . 'html/table/object_table/object_table.class.php';
/**
 * This class represents a table with learning objects which are candidates for
 * publication.
 */
class ObjectPublicationTable extends ObjectTable
{
	const DEFAULT_NAME = 'publication_table';

	function ObjectPublicationTable($parent, $owner, $types, $condition)
	{
		$data_provider = new ObjectPublicationTableDataProvider($parent, $owner, $types, $condition);
		$column_model = new ObjectPublicationTableColumnModel();
		$cell_renderer = new ObjectPublicationTableCellRenderer($parent);
		parent :: __construct($data_provider, ObjectPublicationTable :: DEFAULT_NAME, $column_model, $cell_renderer);
		$actions = array();
		
		$actions[] = new ObjectTableFormAction(Tool :: ACTION_DELETE, Translation :: get('RemoveSelected'));
		$actions[] = new ObjectTableFormAction(Tool :: ACTION_MOVE_SELECTED_TO_CATEGORY, Translation :: get('MoveSelected'), false);
		
		$this->set_form_actions($actions);
	}
}
?>