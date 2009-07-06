<?php
/**
 * @package users.lib.usermanager.component.whois_online
 */
require_once dirname(__FILE__) . '/whois_online_table_column_model.class.php';
require_once Path :: get_user_path() . 'lib/user_table/default_user_table_cell_renderer.class.php';
require_once Path :: get_user_path() . 'lib/user.class.php';
require_once Path :: get_user_path() . 'lib/user_manager/user_manager.class.php';
/**
 * Cell renderer for the user object browser table
 */
class WhoisOnlineTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function WhoisOnlineTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        // Add special features here
        switch ($column->get_object_property())
        {
            case User :: PROPERTY_OFFICIAL_CODE :
                return $user->get_official_code();
            // Exceptions that need post-processing go here ...
            case User :: PROPERTY_STATUS :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdmin');
                }
                if ($user->get_status() == '1')
                {
                    return Translation :: get('CourseAdmin');
                }
                else
                {
                    return Translation :: get('Student');
                }
            case User :: PROPERTY_PLATFORMADMIN :
                if ($user->get_platformadmin() == '1')
                {
                    return Translation :: get('PlatformAdmin');
                }
                else
                {
                    return '';
                }
            case User :: PROPERTY_PICTURE_URI :
                if ($user->get_picture_uri())
                {
                    return '<a href="' . $this->browser->get_url(array('uid' => $user->get_id())) . '">' . '<img style="max-width: 100px; max-height: 100px;" src="' . $user->get_full_picture_url() . '" alt="' . Translation :: get('UserPic') . '" /></a>';
                }
                return '';
        }
        return parent :: render_cell($column, $user);
    }

}
?>