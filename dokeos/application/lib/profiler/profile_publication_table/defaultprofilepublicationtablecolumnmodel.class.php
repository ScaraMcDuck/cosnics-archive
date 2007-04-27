<?php
/**
 * @package repository.publicationtable
 */
require_once dirname(__FILE__).'/profilepublicationtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/profilepublicationtablecolumn.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject.class.php';
require_once dirname(__FILE__).'/../../../../users/lib/user.class.php';
require_once dirname(__FILE__).'/../profilepublication.class.php';

/**
 * TODO: Add comment
 */
class DefaultProfilePublicationTableColumnModel extends ProfilePublicationTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultProfilePublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return LearningObjectTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
		$columns[] = new ProfilePublicationTableColumn(User :: PROPERTY_USERNAME, true);
		$columns[] = new ProfilePublicationTableColumn(User :: PROPERTY_LASTNAME, true);
		$columns[] = new ProfilePublicationTableColumn(User :: PROPERTY_FIRSTNAME, true);
		$columns[] = new ProfilePublicationTableColumn(ProfilePublication :: PROPERTY_PROFILE, true);
		return $columns;
	}
}
?>