<?php
/**
 * @package repository.usertable
 */

require_once dirname(__FILE__).'/usertablecellrenderer.class.php';
require_once dirname(__FILE__).'/../user.class.php';
/**
 * TODO: Add comment
 */
class DefaultUserTableCellRenderer implements UserTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultUserTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $user)
	{
		if ($property = $column->get_user_property())
		{
			switch ($property)
			{
				case User :: PROPERTY_USER_ID :
					return $user->get_user_id();
				case User :: PROPERTY_LASTNAME :
					return $user->get_lastname();
				case User :: PROPERTY_FIRSTNAME :
					return $user->get_firstname();
				case User :: PROPERTY_USERNAME :
					return $user->get_username();
				case User :: PROPERTY_EMAIL :
					return $user->get_email();
				case User :: PROPERTY_STATUS :
					return $user->get_status();
				case User :: PROPERTY_PLATFORMADMIN :
					return $user->get_platformadmin();
				case User :: PROPERTY_OFFICIAL_CODE :
					return $user->get_official_code();
				case User :: PROPERTY_LANGUAGE :
					return $user->get_language();
				case User :: PROPERTY_VERSION_QUOTA :
					return $user->get_version_quota();
			}
		}
		return '&nbsp;';
	}
}
?>