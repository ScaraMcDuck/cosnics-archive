<?php
/**
 * Class representing a reporting block
 *
 * @author: Michael Kyndt
 */

class ReportingBlock{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_ID = 'id';
    const PROPERTY_NAME = 'name';
    const PROPERTY_APPLICATION = 'application';
    const PROPERTY_FUNCTION = 'function';
    const PROPERTY_DISPLAYMODE = 'displaymode';
    const PROPERTY_WIDTH = 'width';
    const PROPERTY_HEIGHT = 'height';

    private $properties, $data,$params;

    public function ReportingBlock($properties = array())
    {
        $this->properties = $properties;
    }

     /**
     * Gets a default property by name.
     * @param string $name The name of the property.
     */
    function get_default_property($name)
    {
        return $this->properties[$name];
    }

    /**
     * Gets the default properties
     * @return array An associative array containing the properties.
     */
    function get_default_properties()
    {
        return $this->properties;
    }

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array (
            self :: PROPERTY_ID,
            self :: PROPERTY_NAME,
            self :: PROPERTY_APPLICATION,
            self :: PROPERTY_FUNCTION,
            self :: PROPERTY_DISPLAYMODE,
            self :: PROPERTY_WIDTH,
            self :: PROPERTY_HEIGHT
        );
    }

    /**
     * Sets a default property by name.
     * @param string $name The name of the property.
     * @param mixed $value The new value for the property.
     */
    function set_default_property($name, $value)
    {
        $this->properties[$name] = $value;
    }

    /**
     * Sets the default properties of this class
     */
    function set_default_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * Retrieves the data for this block
     */
    private function retrieve_data()
    {
        //require_once($this->get_applicationUrl());
        $base_path = (Application :: is_application($this->get_application()) ? Path :: get_application_path().'lib/' : Path :: get(SYS_PATH));

        $file = $base_path .$this->get_application(). '/reporting/reporting_'.$this->get_application().'.class.php';
        require_once $file;
        $this->data = call_user_func('Reporting'.$this->get_application().'::'.$this->get_function(), $this->get_function_parameters());
    }

    /**
     * Creates the block in the database
     */
    public function create()
    {
        $repdmg = ReportingDataManager :: get_instance();
        $this->set_id($repdmg->get_next_reporting_block_id());
        return $repdmg->create_reporting_block($this);
    }

    /**
     * Updates the block in the database
     */
    public function update()
    {
        $repdmg = ReportingDataManager :: get_instance();
        return $repdmg->update_reporting_block($this);
    }

    /**
     * Returns all available displaymodes
     */
    public function get_displaymodes()
    {
        $data = $this->get_data();
        $datadescription = $data[1];
        $chartdata = $data[0];
        $names = sizeof($chartdata);
        $series = sizeof($datadescription["Values"]);

        $modes = array();
        $modes["Text"] = Translation :: get('Text');
        $modes["Table"] = Translation :: get('Table');
        if($series == 1)
        {
            $modes["Chart:Pie"] = Translation :: get('Pie');
            if($names > 2)
            {
                $modes["Chart:Bar"] = Translation :: get('Bar');
                $modes["Chart:Line"] = Translation :: get('Line');
                $modes["Chart:FilledCubic"] = Translation :: get('FilledCubic');
            }
        }else
        {
            $modes["Chart:Bar"] = Translation :: get('Bar');
            $modes["Chart:Line"] = Translation :: get('Line');
            $modes["Chart:FilledCubic"] = Translation :: get('FilledCubic');
        }

        return $modes;
    }

    public function get_export_links()
    {
        require_once Path :: get_library_path().'export/export.class.php';
        $list = Export::get_supported_filetypes(array('ical'));

        $array = array();

        foreach ($list as $export_format) {
            $arr = array();
            $file = Theme :: get_common_image_path().'export_'.$export_format.'.png';
            $sys_file = Theme :: get_instance()->get_path(SYS_IMG_PATH) .'common/export_'.$export_format.'.png';
            if(!file_exists($sys_file))
                $file = Theme :: get_common_image_path().'export_unknown.png';
            $arr[] = '<a href="index_reporting.php?'.ReportingManager::PARAM_ACTION.'='.ReportingManager::ACTION_EXPORT.'&'.ReportingManager::PARAM_REPORTING_BLOCK_ID.'='.$this->get_id().'&'.ReportingManager::PARAM_EXPORT_TYPE.'='.$export_format.'" />';
            //$arr[] = '<img src="'.$file.'" border="0" title="'.$export_format.'" alt="'.$export_format.'" width="12" height="12" />';
            $arr[] = $export_format;
            $arr[] = '</a>';

            $array[] = implode("\n", $arr);
        }

        $return = Translation :: get('Export').': ';
        $return .= implode('|', $array);

        return $return;
    }

    /**
     * Getters and setters
     */

    public function get_data()
    {
        if(!$this->data)
        {
            $this->retrieve_data();
        }
        return $this->data;
    }

    public function add_function_parameter($key,$value)
    {
        $this->params[$key] = $value;
    }

    public function remove_function_parameter($key)
    {
        unset($this->params[$key]);
    }

    public function set_function_parameters($params)
    {
        $this->params = $params;
    }

    public function get_function_parameters()
    {
        return $this->params;
    }

    public function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    public function get_name(){
        return $this->get_default_property(self :: PROPERTY_NAME);
    }
    public function set_name($value){
        $this->set_default_property(self :: PROPERTY_NAME,$value);
    }

    public function get_application(){
        return $this->get_default_property(self :: PROPERTY_APPLICATION);
    }

    public function set_application($value){
        $this->set_default_property(self :: PROPERTY_APPLICATION,$value);
    }

    public function get_function(){
        return $this->get_default_property(self :: PROPERTY_FUNCTION);
    }

    public function set_function($value){
        $this->set_default_property(self :: PROPERTY_FUNCTION,$value);
    }

    public function get_displaymode(){
        return $this->get_default_property(self :: PROPERTY_DISPLAYMODE);
    }

    public function set_displaymode($value){
        $this->set_default_property(self :: PROPERTY_DISPLAYMODE,$value);
    }

    public function get_width()
    {
        return $this->get_default_property(self :: PROPERTY_WIDTH);
    }

    public function set_width($value)
    {
        $this->set_default_property(self :: PROPERTY_WIDTH,$value);
    }

    public function get_height()
    {
        return $this->get_default_property(self :: PROPERTY_HEIGHT);
    }

    public function set_height($value)
    {
        $this->set_default_property(self :: PROPERTY_HEIGHT,$value);
    }

    static function get_table_name()
    {
        return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}//class Reporting_Block
?>
