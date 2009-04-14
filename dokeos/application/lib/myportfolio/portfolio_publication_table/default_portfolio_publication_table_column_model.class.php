<?php
/**
 * @package application.lib.profiler.profile_publication_table
 */
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column_model.class.php';
require_once Path :: get_library_path() . 'html/table/object_table/object_table_column.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object.class.php';
require_once Path :: get_user_path(). 'lib/user.class.php';
require_once dirname(__FILE__).'/../portfolio_publication.class.php';

class DefaultPortfolioPublicationTableColumnModel extends ObjectTableColumnModel
{
	/**
	 * Constructor
	 */
	function DefaultPortfolioPublicationTableColumnModel()
	{
		parent :: __construct(self :: get_default_columns(), 1);
	}
	/**
	 * Gets the default columns for this model
	 * @return ProfileTableColumn[]
	 */
	private static function get_default_columns()
	{
		$columns = array();
//		$columns[] = new ObjectTableColumn(User :: PROPERTY_USERNAME, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_LASTNAME, true);
		$columns[] = new ObjectTableColumn(User :: PROPERTY_FIRSTNAME, true);
		$columns[] = new ObjectTableColumn(PortfolioPublication :: PROPERTY_ITEM, true);
		return $columns;
	}
}
?>