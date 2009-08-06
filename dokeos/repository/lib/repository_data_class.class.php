<?php
require_once Path :: get_common_path() . 'data_class.class.php';
require_once dirname(__FILE__) . '/repository_data_manager.class.php';

abstract class RepositoryDataClass extends DataClass
{
    const PROPERTY_CREATED         = 'created';
    const PROPERTY_MODIFIED        = 'modified';
    
    /*************************************************************************/
    
	public function RepositoryDataClass($defaultProperties = array ()) 
	{
		parent :: DataClass($defaultProperties);
		
		
	}
	
	
	/*************************************************************************/
	
	public function set_creation_date($created)
	{
	    if(isset($created))
	    {
	        $this->set_default_property(self :: PROPERTY_CREATED, $created);
	    }
	}
	
	public function get_creation_date()
	{
	    return $this->get_default_property(self :: PROPERTY_CREATED);
	}
	
	
	/*************************************************************************/
    
    public function set_modification_date($modified)
	{
	    if(isset($modified))
	    {
	        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
	    }
	}
	
	public function get_modification_date()
	{
	    return $this->get_default_property(self :: PROPERTY_MODIFIED);
	}
	
	
	/*************************************************************************/
	
	public function get_data_manager()
	{
	    return RepositoryDataManager :: get_instance();	
	}
	
	static function get_default_property_names($extended_property_names = array())
	{
	    $extended_property_names[] = self :: PROPERTY_CREATED;
	    $extended_property_names[] = self :: PROPERTY_MODIFIED;
	    
		return parent :: get_default_property_names($extended_property_names);
	}
	
	/*************************************************************************/
	
	abstract static function get_table_name();	

}
?>