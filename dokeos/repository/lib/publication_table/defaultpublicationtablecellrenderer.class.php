<?php
/**
 * @package repository.publicationtable
 */

require_once dirname(__FILE__).'/publicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';
require_once dirname(__FILE__).'/../learningobjectpublicationattributes.class.php';
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
				case LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_OBJECT :
					return $learning_object_publication->get_publication_object_id();
				case LearningObjectPublicationAttributes :: PROPERTY_APPLICATION :
					return $learning_object_publication->get_application();
				case LearningObjectPublicationAttributes :: PROPERTY_LOCATION :
					return $learning_object_publication->get_location();
				case LearningObject :: PROPERTY_TITLE :
					return $learning_object_publication->get_publication_object()->get_title();
				case LearningObjectPublicationAttributes :: PROPERTY_PUBLICATION_DATE :
					return date('Y-m-d, H:i', $learning_object_publication->get_publication_date());
			}
		}
		return '&nbsp;';
	}
}
?>