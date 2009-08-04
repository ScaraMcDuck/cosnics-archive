<?php
/**
 * $Id: course_settingstool.class.php 9222 2006-09-15 09:19:38Z bmol $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */

require_once Path :: get_common_path() . 'html/formvalidator/form_validator_page.class.php';

/**
 * This abstract class defines a page which is used in a archive trackers wizard.
 * @author Sven Vanpoucke
 */
abstract class ArchiveWizardPage extends FormValidatorPage
{
    /**
     * The Component which the wizard runs.
     */
    private $parent;
    
    /**
     * The name of the page
     */
    private $name;

    /**
     * Constructor
     * @param string $name A unique name of this page in the wizard
     * @param TrackingManagerArchiveComponent $parent The component in which the wizard runs
     */
    public function ArchiveWizardPage($name, $parent)
    {
        $this->parent = $parent;
        $this->name = $name;
        parent :: FormValidatorPage($name, 'post');
       	$this->updateAttributes(array('action' => $parent->get_url()));
    }

    /**
     * Returns the Component in which this wizard runs
     * @return TrackingManagerArchiveComponent
     */
    function get_parent()
    {
        return $this->parent;
    }

    /**
     * Returns the name of the page
     */
    function get_name()
    {
        return $this->name;
    }
}
?>