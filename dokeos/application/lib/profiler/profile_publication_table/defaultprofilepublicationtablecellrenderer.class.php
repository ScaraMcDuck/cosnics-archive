<?php
/**
 * @package repository.publicationtable
 */

require_once dirname(__FILE__).'/profilepublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../profilepublication.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/user.class.php';
/**
 * TODO: Add comment
 */
class DefaultProfilePublicationTableCellRenderer implements ProfilePublicationTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultProfilePublicationTableCellRenderer()
	{
	}
	/**
	 * Renders a table cell
	 * @param LearningObjectTableColumnModel $column The column which should be
	 * rendered
	 * @param Learning Object $learning_object The learning object to render
	 * @return string A HTML representation of the rendered table cell
	 */
	function render_cell($column, $profile_publication)
	{
		if ($property = $column->get_profile_property())
		{
			$user = $profile_publication->get_publication_publisher();
			switch ($property)
			{
				case ProfilePublication :: PROPERTY_PROFILE :
					return $profile_publication->get_publication_object()->get_title();
				case User :: PROPERTY_USERNAME :
					return $user->get_username();
				case User :: PROPERTY_LASTNAME :
					return $user->get_lastname();
				case User :: PROPERTY_FIRSTNAME :
					return $user->get_firstname();
			}
		}
		return '&nbsp;';
	}
}
?>