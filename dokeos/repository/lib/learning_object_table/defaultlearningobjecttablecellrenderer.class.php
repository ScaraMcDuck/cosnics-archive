<?php
/** 
 * @package repository
 */
require_once dirname(__FILE__).'/learningobjecttablecellrenderer.class.php';
require_once dirname(__FILE__).'/../learningobject.class.php';

class DefaultLearningObjectTableCellRenderer implements LearningObjectTableCellRenderer
{
	function DefaultLearningObjectTableCellRenderer()
	{
	}

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
					return '<img src="'.api_get_path(WEB_CODE_PATH).'img/'.$type.'.gif" alt="'.$type.'"/>';
				case LearningObject :: PROPERTY_TITLE :
					return htmlentities($learning_object->get_title());
				case LearningObject :: PROPERTY_DESCRIPTION :
					$description = strip_tags($learning_object->get_description());
					if(strlen($description) > 203)
					{
						$description = substr($description,0,200).'&hellip;';
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