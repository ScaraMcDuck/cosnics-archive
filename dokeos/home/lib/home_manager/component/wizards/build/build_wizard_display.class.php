<?php
/**
 * @package home.homemanager.component
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class BuildWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The repository tool in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function BuildWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $breadcrumbs = array();
        $breadcrumbs[] = array('url' => $this->parent->get_url(array(Application :: PARAM_ACTION => HomeManager :: ACTION_MANAGE_HOME)), 'name' => Translation :: get('Home'));
        $breadcrumbs[] = array('url' => $this->parent->get_url(), 'name' => Translation :: get('BuildHome'));
        
        $this->parent->display_header($breadcrumbs, false, 'home build');
        if (isset($_SESSION['build_message']))
        {
            Display :: normal_message($_SESSION['build_message']);
            unset($_SESSION['build_message']);
        }
        if (isset($_SESSION['build_error_message']))
        {
            Display :: error_message($_SESSION['build_error_message']);
            unset($_SESSION['build_error_message']);
        }
        parent :: _renderForm($current_page);
        $this->parent->display_footer();
    }
}
?>