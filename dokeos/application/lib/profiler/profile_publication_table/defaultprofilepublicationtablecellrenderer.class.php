<?php
/**
 * @package repository.publicationtable
 */

require_once dirname(__FILE__).'/profilepublicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../profilepublication.class.php';
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
			switch ($property)
			{
				case ProfilePublication :: PROPERTY_PROFILE :
					return $profile_publication->get_publication_object()->get_title();
				case ProfilePublication :: PROPERTY_SENDER :
					$user = $profile_publication->get_publication_sender();
					return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
				case ProfilePublication :: PROPERTY_RECIPIENT :
					$user = $profile_publication->get_publication_recipient();
					return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
				case ProfilePublication :: PROPERTY_PUBLISHED :
					return $profile_publication->get_published();
				case ProfilePublication :: PROPERTY_STATUS :
					return $profile_publication->get_status();
			}
		}
		return '&nbsp;';
	}
}
?>