<?php
/**
 * @package application.lib.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

require_once Path :: get_library_path() . 'html/table/object_table/object_table_cell_renderer.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once dirname(__FILE__).'/../personal_message_publication.class.php';

class DefaultPmPublicationTableCellRenderer implements ObjectTableCellRenderer
{
	/**
	 * Constructor
	 */
	function DefaultPmPublicationTableCellRenderer()
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
		switch ($column->get_name())
		{
			case PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE :
				return $personal_message_publication->get_publication_object()->get_title();
			case PersonalMessagePublication :: PROPERTY_SENDER :
				$user = $personal_message_publication->get_publication_sender();
				if($user)
					return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
				return Translation :: get('SenderUnknown');
			case PersonalMessagePublication :: PROPERTY_RECIPIENT :
				$user = $personal_message_publication->get_publication_recipient();
				if($user)
					return $user->get_firstname() . '&nbsp;' . $user->get_lastname();
				return Translation :: get('RecipientUnknown');
			case PersonalMessagePublication :: PROPERTY_PUBLISHED :
				return $personal_message_publication->get_published();
			case PersonalMessagePublication :: PROPERTY_STATUS :
				return $personal_message_publication->get_status();
			default :
			    return '&nbsp;';
		}
	}

	function render_id_cell($object)
	{
		return $object->get_id();
	}
}
?>