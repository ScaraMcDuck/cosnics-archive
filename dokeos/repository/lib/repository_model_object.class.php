<?php
abstract class RepositoryModelObject
{
    const PROPERTY_ID              = 'id';
    const PROPERTY_CREATED         = 'created';
    const PROPERTY_MODIFIED        = 'modified';
    
    const NO_UID                   = -1;
    
    protected $defaultProperties   = array();
    
    function RepositoryModelObject($id = null, $defaultProperties = array ())
    {
        if(is_array($defaultProperties))
	    {
	        $this->defaultProperties = $defaultProperties;
	    }
	    else
	    {
	        $this->defaultProperties = array();
	    }
	    
	    $this->set_id($id);
    }
    
    /*************************************************************************/
    
    function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
    
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	
	function get_default_properties()
	{
	    return $this->defaultProperties;
	}
	
	function set_default_properties($default_properties)
	{
	    return $this->defaultProperties = $default_properties;
	}
    
    /*************************************************************************/
    
    function set_id($id)
	{
	    if(isset($id) && is_numeric($id))
	    {
	        $this->set_default_property(self :: PROPERTY_ID, $id);
	    }
	}
	
	function get_id()
	{
	    return $this->defaultProperties[self :: PROPERTY_ID];
	}
	
	function is_identified()
	{
	    $id = $this->get_id();
	    if(isset($id) && $id != self :: NO_UID)
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	/*************************************************************************/
    
    function set_creation_date($created)
	{
	    if(isset($created))
	    {
	        $this->set_default_property(self :: PROPERTY_CREATED, $created);
	    }
	}
	
	function get_creation_date()
	{
	    return $this->defaultProperties[self :: PROPERTY_CREATED];
	}
	
	/*************************************************************************/
    
    function set_modification_date($modified)
	{
	    if(isset($modified))
	    {
	        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
	    }
	}
	
	function get_modification_date()
	{
	    return $this->defaultProperties[self :: PROPERTY_MODIFIED];
	}
	
	/*************************************************************************/
	
	abstract static function get_table_name();
	abstract static function get_default_property_names();
	
	function save()
	{
	    if($this->is_identified())
	    {
	        return $this->update();
	    }
	    else
	    {
	        return $this->create();
	    }
	}
	
	/**
	 * Update an existing record
	 * @return bool
	 */
	abstract function update();
	
	/**
	 * Create a new record
	 * @return bool
	 */
	abstract function create();
	
	/**
	 * Delete an existing record
	 * @return bool
	 */
	abstract function delete();
}
?>