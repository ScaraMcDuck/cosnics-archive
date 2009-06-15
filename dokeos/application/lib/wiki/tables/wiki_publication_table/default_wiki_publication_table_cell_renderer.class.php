<?php
/**
 * @package wiki.tables.wiki_publication_table
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../wiki_publication.class.php';

/**
 * Default cell renderer for the wiki_publication table
 * @author Sven Vanpoucke & Stefan Billiet
 */
class DefaultWikiPublicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultWikiPublicationTableCellRenderer()
	{
	}

	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param WikiPublication $wiki_publication - The wiki_publication
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $wiki_publication)
	{
		if ($property = $column->get_object_property())
		{
			switch ($property)
			{
                case LearningObject :: PROPERTY_TITLE :
                    return $wiki_publication->get_learning_object()->get_title();
                case LearningObject :: PROPERTY_DESCRIPTION:
                    return $wiki_publication->get_learning_object()->get_description();
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