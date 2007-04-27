<?php
/**
 * @package repository.publicationtable
 */
require_once dirname(__FILE__).'/profilepublicationtablecolumnmodel.class.php';
require_once dirname(__FILE__).'/profilepublicationtablecolumn.class.php';
require_once dirname(__FILE__).'/../../../../repository/lib/learningobject.class.php';
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
		$columns[] = new ProfilePublicationTableColumn(ProfilePublication :: PROPERTY_STATUS, true);
		$columns[] = new ProfilePublicationTableColumn(ProfilePublication :: PROPERTY_PROFILE, true);
		$columns[] = new ProfilePublicationTableColumn(ProfilePublication :: PROPERTY_PUBLISHED, true);
		return $columns;
	}
}
?>