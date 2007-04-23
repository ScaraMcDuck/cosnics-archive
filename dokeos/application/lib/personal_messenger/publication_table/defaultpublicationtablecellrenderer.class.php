<?php
/**
 * @package repository.publicationtable
 */

require_once dirname(__FILE__).'/publicationtablecellrenderer.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../personalmessagepublication.class.php';
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
	function render_cell($column, $personal_message_publication)
	{
		if ($property = $column->get_personal_message_property())
		{
			switch ($property)
			{
				case PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE :
					return $personal_message_publication->get_publication_object()->get_title();
				case PersonalMessagePublication :: PROPERTY_SENDER :
					$user = $personal_message_publication->get_publication_sender();
					return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
				case PersonalMessagePublication :: PROPERTY_RECIPIENT :
					$user = $personal_message_publication->get_publication_recipient();
					return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
				case PersonalMessagePublication :: PROPERTY_PUBLISHED :
					return $personal_message_publication->get_published();
				case PersonalMessagePublication :: PROPERTY_STATUS :
					return $personal_message_publication->get_status();
			}
		}
		return '&nbsp;';
	}
}
?>