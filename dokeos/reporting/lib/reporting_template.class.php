<?php
/**
 * Extendable class for the reporting templates
 * This contains the general shared template properties such as
 *      Properties (name, description, etc)
 *      Layout (header,menu, footer)
 *
 * @author Michael Kyndt
 */
require_once Path :: get_reporting_path().'lib/reporting.class.php';

abstract class ReportingTemplate {

    const REPORTING_BLOCK_VISIBLE = 1;
    const REPORTING_BLOCK_INVISIBLE = 0;
    protected $parent;
    /*
     * array with all the reporting block and specific properties such as
     *  - visible
     *
     * @todo add 'zone'
     */
    protected $reporting_blocks = array();
    protected $id;

    function ReportingTemplate()
    {
        
    }//ReportingTemplateProperties
    
    /*
     * Layout
     */

    /**
     * The reporting template header
     * @return html representing the header
     */
    function get_header()
    {
        
    }//get_header

    /**
     * Generates a menu from the reporting blocks within the reporting template
     * @return html representing the menu
     */
    function get_menu()
    {
        foreach($this->retrieve_reporting_blocks() as $key => $value)
        {
            $html[] = '<a href="' . $this->parent->get_url(array('s' => $value[0]->get_name(),'template' => $this->get_registration_id())) . '">'.Translation :: get($value[0]->get_name()).'</a><br />';
        }
        return implode("\n", $html);
    }

    /**
     * The reporting template footer
     * @return html representing the footer
     */
    function get_footer()
    {
        return '<script type="text/javascript" src="'. Path :: get(WEB_LIB_PATH) . 'javascript/reporting_charttype.js' .'"></script>';
    }//get_footer
    
    /*
     * Properties
     */

    /**
     * Gets the properties for this template (name, description, platform)
     * @return an array of properties
     */
    abstract static function get_properties();

    /**
     * Sets the id under which this template is registered
     * @param int $value
     */
    function set_registration_id($value)
    {
        $this->id = $value;
    }

    /**
     * Gets the id under which this template is registered
     * @return the id under which this template is registered
     */
    function get_registration_id()
    {
        return $this->id;
    }

    /*
     * Reporting blocks
     */

    /**
     * Adds a reporting block to this template
     * @param ReportingBlock $reporting_block
     * @param int $visible
     */
    function add_reporting_block($reporting_block,$visible)
    {
        array_push($this->reporting_blocks,array($reporting_block,$visible));
    }

    /**
     * Sets the visible value to 1 for this reporting block & 0 for the rest
     * @param String $name
     */
    function show_reporting_block($name)
    {
        foreach($this->reporting_blocks as $key => $value)
        {
            if($value[0]->get_name() == $name)
            {
                //add constant if visible
                // ReportingTemplate :: VISIBLE
                $value[1] = self :: REPORTING_BLOCK_VISIBLE;
            }else
            {
                $value[1] = self :: REPORTING_BLOCK_INVISIBLE;
            }
            $this->reporting_blocks[$key] = $value;
        }
    }

    /**
     * Generates all the visible reporting blocks
     * @return html
     */
    function get_visible_reporting_blocks()
    {
        foreach($this->retrieve_reporting_blocks() as $key => $value)
        {
            // check if reporting block is visible
            if($value[1] == self :: REPORTING_BLOCK_VISIBLE)
            {
                $html[] = Reporting :: generate_block($value[0]);
                $html[] = '<br />';
            }
        }
        return implode("\n", $html);
    }

    /**
     * Returns all reporting blocks for this reporting template
     * @return an array of reporting blocks
     */
    function retrieve_reporting_blocks()
    {
        return $this->reporting_blocks;
    }
}//ReportingTemplateProperties
?>
