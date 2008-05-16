<?php
/**
 * @package repository.usertable
 */

require_once dirname(__FILE__).'/classgrouptablecellrenderer.class.php';
require_once dirname(__FILE__).'/../classgroup.class.php';
/**
 * TODO: Add comment
 */
class DefaultClassgroupTableCellRenderer implements ClassgroupTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultClassgroupTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $classgroup)
	{
		if ($property = $column->get_classgroup_property())
		{
			switch ($property)
			{
				case Classgroup :: PROPERTY_ID :
					return $classgroup->get_id();
				case Classgroup :: PROPERTY_NAME :
					return $classgroup->get_name();
				case Classgroup :: PROPERTY_DESCRIPTION :
					return $classgroup->get_description();
			}
		}
		return '&nbsp;';
	}
}
?>