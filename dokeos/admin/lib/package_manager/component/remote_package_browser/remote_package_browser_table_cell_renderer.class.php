<?php
/**
 * @package repository.repositorymanager
 */
require_once Path :: get_admin_path() . 'lib/package_manager/component/remote_package_browser/remote_package_browser_table_column_model.class.php';
require_once Path :: get_admin_path() . 'lib/tables/remote_package_table/default_remote_package_table_cell_renderer.class.php';
require_once Path :: get_admin_path() . 'lib/remote_package.class.php';
require_once Path :: get_admin_path() . 'lib/admin_manager/admin_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RemotePackageBrowserTableCellRenderer extends DefaultRemotePackageTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RemotePackageBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $remote_package)
    {
        if ($column === RemotePackageBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($remote_package);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case RemotePackage :: PROPERTY_DESCRIPTION :
                $description = strip_tags(parent :: render_cell($column, $remote_package));
                return DokeosUtilities :: truncate_string($description);
        }
        
        return parent :: render_cell($column, $remote_package);
    }

    /**
     * Gets the action links to display
     * @param LearningObject $learning_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($remote_package)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_remote_package_installation_url($remote_package), 'label' => Translation :: get('Install'), 'img' => Theme :: get_image_path() . 'action_install.png');
        
        return DokeosUtilities :: build_toolbar($toolbar_data);
    }
}
?>