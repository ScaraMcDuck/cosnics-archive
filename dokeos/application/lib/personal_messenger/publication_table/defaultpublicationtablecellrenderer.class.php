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
					return $personal_message_publication->get_personal_message();
				case PersonalMessagePublication :: PROPERTY_SENDER :
					return $personal_message_publication->get_sender();
				case PersonalMessagePublication :: PROPERTY_RECIPIENT :
					return $personal_message_publication->get_recipient();
				case PersonalMessagePublication :: PROPERTY_PUBLISHED :
					return $personal_message_publication->get_published();
			}
		}
		return '&nbsp;';
	}
}
?>