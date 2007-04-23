<?php
/**
 * @package repository.publicationtable
 */
require_once dirname(__FILE__).'/publicationtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/publicationtablecolumn.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../personalmessagepublication.class.php';

/**
 * TODO: Add comment
 */
class DefaultPublicationTableColumnModel extends PublicationTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultPublicationTableColumnModel($folder)
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
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_STATUS, true);
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE, true);
		
		switch ($folder)
		{
			case PersonalMessenger :: ACTION_FOLDER_INBOX :
				$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_SENDER, true);
				break;
			case PersonalMessenger :: ACTION_FOLDER_OUTBOX :
				$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_RECIPIENT, true);
				break;
			default :
				$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_SENDER, true);
		}
		
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_PUBLISHED, true);
		return $columns;
	}
}
?>