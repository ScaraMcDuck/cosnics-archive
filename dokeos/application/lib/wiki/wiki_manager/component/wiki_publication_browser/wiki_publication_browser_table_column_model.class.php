<?php
/**
 * @package wiki.tables.wiki_publication_table
 */

require_once dirname(__FILE__).'/../../../tables/wiki_publication_table/default_wiki_publication_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../wiki_publication.class.php';

/**
 * Table column model for the wiki_publication browser table
 * @author Sven Vanpoucke & Stefan Billiet
 */

class WikiPublicationBrowserTableColumnModel extends DefaultWikiPublicationTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;

	/**
	 * Constructor
	 */
	function WikiPublicationBrowserTableColumnModel()
	{
		parent :: __construct();
		$this->set_default_order_column(1);
		$this->add_column(self :: get_modification_column());
	}

	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new ObjectTableColumn('');
		}
		return self :: $modification_column;
	}
}
?>