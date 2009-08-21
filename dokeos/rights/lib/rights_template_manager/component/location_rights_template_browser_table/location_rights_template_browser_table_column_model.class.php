<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../../../tables/rights_template_table/default_rights_template_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../rights_template.class.php';
/**
 * Table column model for the user browser table
 */
class LocationRightsTemplateBrowserTableColumnModel extends DefaultRightsTemplateTableColumnModel
{
	/**
	 * The tables modification column
	 */
	private static $modification_column;
	private static $rights_columns;
	private $browser;
	
	/**
	 * Constructor
	 */
	function LocationRightsTemplateBrowserTableColumnModel($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
		$this->set_default_order_column(1);
		$this->add_rights_columns();
//		$this->add_column(self :: get_modification_column());
	}
	/**
	 * Gets the modification column
	 * @return LearningObjectTableColumn
	 */
	static function get_modification_column()
	{
		if (!isset(self :: $modification_column))
		{
			self :: $modification_column = new StaticTableColumn('');
		}
		return self :: $modification_column;
	}
	
    static function is_rights_column($column)
    {
        return in_array($column, self :: $rights_columns);
    }

    function add_rights_columns()
    {
	    $rights = $this->browser->get_rights();

        foreach ($rights as $right_name => $right_id)
        {
            $column_name = DokeosUtilities :: underscores_to_camelcase(strtolower($right_name));
            $column = new StaticTableColumn(Translation :: get($column_name));
            $this->add_column($column);
            self :: $rights_columns[] = $column;
        }
    }
}
?>
