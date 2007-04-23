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
	function DefaultPublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 3);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_PERSONAL_MESSAGE, true);
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_SENDER, true);
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_RECIPIENT, true);
		$columns[] = new PublicationTableColumn(PersonalMessagePublication :: PROPERTY_PUBLISHED, true);
		return $columns;
	}
}
?>