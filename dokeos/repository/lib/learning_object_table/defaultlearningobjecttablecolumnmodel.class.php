<?php
/**
 * $Id$
 * @package repository.learningobjecttable
 */
require_once dirname(__FILE__).'/learningobjecttablecolumnmodel.class.php';
require_once dirname(__FILE__).'/learningobjecttablecolumn.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';

/**
 * This is the default column model, used when a LearningObjectTable does not
 * provide its own model.
 *
 * The default model contains the following columns:
 *
 * - The type of the learning object
 * - The title of the learning object
 * - The description of the learning object
 * - The date when the learning object was last modified
 *
 * Although this model works best in conjunction with the default cell
 * renderer, it can be used with any LearningObjectTableCellRenderer.
 *
 * @see LearningObjectTable
 * @see LearningObjectTableColumnModel
 * @see DefaultLearningObjectTableCellRenderer
 * @author Tim De Pauw
 */
class DefaultLearningObjectTableColumnModel extends LearningObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultLearningObjectTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TYPE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_TITLE, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_DESCRIPTION, true);
		$columns[] = new LearningObjectTableColumn(LearningObject :: PROPERTY_MODIFICATION_DATE, true);
		return $columns;
	}
}
?>