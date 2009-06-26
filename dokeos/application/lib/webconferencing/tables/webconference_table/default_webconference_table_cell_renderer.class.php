<?php
/**
 * @package webconferencing.tables.webconference_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../webconference.class.php';

/**
 * Default cell renderer for the webconference table
 * @author Stefaan Vanbillemont
 */
class DefaultWebconferenceTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultWebconferenceTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Webconference $webconference - The webconference
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $webconference)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
				case Webconference :: PROPERTY_ID :
					return $webconference->get_id();
				case Webconference :: PROPERTY_CONFNAME :
					return $webconference->get_confname();
				case Webconference :: PROPERTY_DESCRIPTION :
					return $webconference->get_description();
				case Webconference :: PROPERTY_DURATION :
					return $webconference->get_duration();
			}
		}
		return '&nbsp;';
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>