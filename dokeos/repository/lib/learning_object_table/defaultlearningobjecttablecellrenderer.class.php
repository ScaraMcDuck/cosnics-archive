<?php
/**
 * $Id$
 * @package repository.learningobjecttable
 */

require_once dirname(__FILE__).'/learningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../learning_object.class.php';
/**
 * This is the default cell renderer, used when a LearningObjectTable does not
 * provide its own renderer.
 *
 * The default renderer provides a custom rendering method for the following
 * columns:
 *
 * - The ID of the learning object
 *   Displays the ID.
 * - The type of the learning object
 *   Displays the icon that corresponds to the learning object type.
 * - The title of the learning object
 *   Displays the title.
 * - The description of the learning object
 *   Strips HTML tags from the description of the learning object and displays
 *   the first 200 characters of the resulting string.
 * - The date when the learning object was created
 *   Displays a localized version of the date.
 * - The date when the learning object was last modified
 *   Displays a localized version of the date.
 *
 * Any other column type will result in an empty cell.
 *
 * @see LearningObjectTable
 * @see LearningObjectTableCellRenderer
 * @see DefaultLearningObjectTableColumnModel
 * @author Tim De Pauw
 */
class DefaultLearningObjectTableCellRenderer implements LearningObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultLearningObjectTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $learning_object)
	{
		if ($property = $column->get_learning_object_property())
		{
			switch ($property)
			{
				case LearningObject :: PROPERTY_ID :
					return $learning_object->get_id();
				case LearningObject :: PROPERTY_TYPE :
					$type = $learning_object->get_type();
					$icon = $learning_object->get_icon_name();
					return '<img src="'.Theme :: get_common_img_path() . 'learning_object/' .$icon.'.png" alt="'.htmlentities(Translation :: get(LearningObject :: type_to_class($type).'TypeName')).'"/>';
				case LearningObject :: PROPERTY_TITLE :
					return htmlspecialchars($learning_object->get_title());
				case LearningObject :: PROPERTY_DESCRIPTION :
					$description = strip_tags($learning_object->get_description());
					if(strlen($description) > 203)
					{
						mb_internal_encoding("UTF-8");
						$description = mb_substr(strip_tags($learning_object->get_description()),0,200).'&hellip;';
					}
					return $description;
				case LearningObject :: PROPERTY_CREATION_DATE :
					// TODO: i18n
					return date('Y-m-d, H:i', $learning_object->get_creation_date());
				case LearningObject :: PROPERTY_MODIFICATION_DATE :
					// TODO: i18n
					return date('Y-m-d, H:i', $learning_object->get_creation_date());
			}
		}
		return '&nbsp;';
	}
}
?>