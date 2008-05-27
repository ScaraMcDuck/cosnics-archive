<?php
/**
 * @package application.lib.personal_messenger.pm_publication_table
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/pm_publication_table_column_model.class.php';
require_once dirname(__FILE__).'/pm_publication_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once dirname(__FILE__).'/../personal_message_publication.class.php';

class DefaultPmPublicationTableColumnModel extends PmPublicationTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultPmPublicationTableColumnModel($folder)
	{
		parent :: __construct(self :: get_default_columns($folder), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns($folder)
	{
		$columns = array();
		$columns[] = new PmPublicationTableColumn(PersonalMessagePublication :: PROPERTY_STATUS, true);
		$columns[] = new PmPublicationTableColumn(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE, true);
		
		switch ($folder)
		{
			case PersonalMessenger :: ACTION_FOLDER_INBOX :
				$columns[] = new PmPublicationTableColumn(PersonalMessagePublication :: PROPERTY_SENDER, true);
				break;
			case PersonalMessenger :: ACTION_FOLDER_OUTBOX :
				$columns[] = new PmPublicationTableColumn(PersonalMessagePublication :: PROPERTY_RECIPIENT, true);
				break;
			default :
				$columns[] = new PmPublicationTableColumn(PersonalMessagePublication :: PROPERTY_SENDER, true);
		}
		
		$columns[] = new PmPublicationTableColumn(PersonalMessagePublication :: PROPERTY_PUBLISHED, true);
		return $columns;
	}
}
?>