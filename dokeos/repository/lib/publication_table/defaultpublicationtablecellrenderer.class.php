<?php
/**
 * @package repository.publicationtable
 */

require_once dirname(__FILE__).'/publicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../learningobjectpublication.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/learningobject.class.php';
/**
 * TODO: Add comment
 */
class DefaultPublicationTableCellRenderer implements PublicationTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultPublicationTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $learning_object_publication)
	{
		if ($property = $column->get_learning_object_property())
		{
			switch ($property)
			{
				case LearningObjectPublication :: PROPERTY_ID :
					return $learning_object_publication->get_id();
				case LearningObjectPublication :: PROPERTY_COURSE_ID :
					return $learning_object_publication->get_course_id();
				case LearningObjectPublication :: PROPERTY_TOOL :
					return $learning_object_publication->get_tool();
				case LearningObject :: PROPERTY_TITLE :
					return $learning_object_publication->get_learning_object()->get_title();
				case LearningObjectPublication :: PROPERTY_PUBLICATION_DATE :
					return $learning_object_publication->get_publication_date();
			}
		}
		return '&nbsp;';
	}
}
?>