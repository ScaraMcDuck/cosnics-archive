<?php
/**
 * Extendable class for the reporting templates
 * This contains the general shared template properties such as
 *      Properties (name, description, etc)
 *      Layout (header,menu, footer)
 *
 * @author Michael Kyndt
 */
abstract class ReportingTemplate {
    
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

    }

    /**
     * The reporting template footer
     * @return html representing the footer
     */
    function get_footer()
    {
        
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
     */
    function set_registration_id()
    {

    }

    /**
     * Gets the id under which this template is registered
     * @return the id under which this template is registered
     */
    function get_registration_id()
    {

    }

    /*
     * Reporting blocks
     */

    /**
     * Adds a reporting block to this template
     */
    function add_reporting_block()
    {

    }

    /**
     * Returns all reporting blocks for this reporting template
     * @return an array of reporting blocks
     */
    function retrieve_reporting_blocks()
    {

    }
}//ReportingTemplateProperties
?>
