<?php
require_once dirname(__FILE__) . '/repository_model_object.class.php';

class LearningObjectMetadataCatalog extends RepositoryModelObject
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_TYPE            = 'type';
	const PROPERTY_VALUE           = 'value';
	const PROPERTY_NAME            = 'name';
	const PROPERTY_SORT            = 'sort';
	
	const CATALOG_LANGUAGE         = 'language';
    const CATALOG_COPYRIGHT        = 'copyright';
    const CATALOG_ROLE             = 'role';
    
	function LearningObjectMetadataCatalog($id = null, $defaultProperties = array ())
	{
	    parent :: __construct($id, $defaultProperties);
	}
	
	/*************************************************************************/
	
	function set_type($type)
	{
	    if(isset($type) && strlen($type) > 0)
	    {
	        $this->set_default_property(self :: PROPERTY_TYPE, $type);
	    }
	}
	
	function get_type()
	{
	    return $this->defaultProperties[self :: PROPERTY_TYPE];
	}
	
	/*************************************************************************/
	
	function set_value($value)
	{
	    if(isset($value))
	    {
	        $this->set_default_property(self :: PROPERTY_VALUE, $value);
	    }
	}
	
	function get_value()
	{
	    return $this->defaultProperties[self :: PROPERTY_VALUE];
	}
	
	/*************************************************************************/
	
	function set_name($value)
	{
	    if(isset($value))
	    {
	        $this->set_default_property(self :: PROPERTY_NAME, $value);
	    }
	}
	
	function get_name()
	{
	    return $this->defaultProperties[self :: PROPERTY_NAME];
	}
	
	/*************************************************************************/
	
	function set_sort($value)
	{
	    if(isset($value))
	    {
	        $this->set_default_property(self :: PROPERTY_SORT, $value);
	    }
	}
	
	function get_sort()
	{
	    return $this->defaultProperties[self :: PROPERTY_SORT];
	}
	
	/*************************************************************************/
	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,   
		                self :: PROPERTY_TYPE, 
		                self :: PROPERTY_VALUE,
		                self :: PROPERTY_NAME,
		                self :: PROPERTY_SORT, 
		                parent :: PROPERTY_CREATED, 
		                parent :: PROPERTY_MODIFIED);
	}
	
	static function get_table_name()
	{
		return DokeosUtilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	
	function create()
	{
	    $dm = RepositoryDataManager :: get_instance();
	    
	    $id = $this->get_id();
	    if(!isset($id) || $id == parent :: NO_UID)
	    {
	        $this->set_id($dm->get_next_learning_object_metadata_catalog_id());
	    }
	    
	    $this->set_creation_date(time());
	    
	    return $dm->create_learning_object_metadata_catalog($this);
	}
	
	function update()
	{
	    $id = $this->get_id();
	    if(!isset($id))
	    {
	       throw new Exception('Learning object metadata catalog could not be saved as its identity is not set');
	    }
	    
	    $this->set_modification_date(time());
	    
	    $dm = RepositoryDataManager :: get_instance();
	    $result = $dm->update_learning_object_metadata_catalog($this);
	    
	    return $result;
	}
	
	function delete()
	{
	    $dm = RepositoryDataManager :: get_instance();
	    $result = $dm->delete_learning_object_metadata_catalog($this);
	    
	    return $result;
	}
	
	/*************************************************************************/
}
?>