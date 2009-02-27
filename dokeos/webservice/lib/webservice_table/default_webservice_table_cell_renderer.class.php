<?php
/**
 * @package repository.usertable
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../webservice_registration.class.php';
/**
 * TODO: Add comment
 */
class DefaultWebserviceTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultWebserviceTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $webservice)
	{ 
		if ($property = $column->get_object_property())
		{ 
			switch ($property)
			{
				case WebserviceRegistration :: PROPERTY_NAME :
					return $webservice->get_name();
				case WebserviceRegistration :: PROPERTY_DESCRIPTION :
					$description = strip_tags($webservice->get_description());
					if(strlen($description) > 203)
					{
						mb_internal_encoding("UTF-8");
						$description = mb_substr(strip_tags($role->get_description()),0,200).'&hellip;';
					}
					return $description;
			}
		}
		return '&nbsp;';
	}
}
?>