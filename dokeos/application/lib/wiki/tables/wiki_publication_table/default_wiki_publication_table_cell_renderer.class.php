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
				case WikiPublication :: PROPERTY_ID :
					return $wiki_publication->get_id();
				case WikiPublication :: PROPERTY_LEARNING_OBJECT :
					return $wiki_publication->get_learning_object();
				case WikiPublication :: PROPERTY_PARENT_ID :
					return $wiki_publication->get_parent_id();
				case WikiPublication :: PROPERTY_CATEGORY :
					return $wiki_publication->get_category();
				case WikiPublication :: PROPERTY_FROM_DATE :
					return $wiki_publication->get_from_date();
				case WikiPublication :: PROPERTY_TO_DATE :
					return $wiki_publication->get_to_date();
				case WikiPublication :: PROPERTY_HIDDEN :
					return $wiki_publication->get_hidden();
				case WikiPublication :: PROPERTY_PUBLISHER :
					return $wiki_publication->get_publisher();
				case WikiPublication :: PROPERTY_PUBLISHED :
					return $wiki_publication->get_published();
				case WikiPublication :: PROPERTY_MODIFIED :
					return $wiki_publication->get_modified();
				case WikiPublication :: PROPERTY_DISPLAY_ORDER :
					return $wiki_publication->get_display_order();
				case WikiPublication :: PROPERTY_EMAIL_SENT :
					return $wiki_publication->get_email_sent();
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