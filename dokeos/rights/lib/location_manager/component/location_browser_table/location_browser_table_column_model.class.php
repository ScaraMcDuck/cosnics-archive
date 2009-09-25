<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__) . '/../../../tables/location_table/default_location_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../location.class.php';
/**
 * Table column model for the user browser table
 */
class LocationBrowserTableColumnModel extends DefaultLocationTableColumnModel
{
    /**
     * The tables modification column
     */
    private static $modification_column;
    private $browser;

    /**
     * Constructor
     */
    function LocationBrowserTableColumnModel($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
		$this->add_column(new ObjectTableColumn(Location :: PROPERTY_TYPE));
		$this->add_column(new ObjectTableColumn(Location :: PROPERTY_LOCKED));
		$this->add_column(new ObjectTableColumn(Location :: PROPERTY_INHERIT));
//        $this->add_column(self :: get_modification_column());
        $this->set_default_order_column(1);
    }

    /**
     * Gets the modification column
     * @return ContentObjectTableColumn
     */
    static function get_modification_column()
    {
        if (! isset(self :: $modification_column))
        {
            self :: $modification_column = new StaticTableColumn('');
        }
        return self :: $modification_column;
    }
}
?>
