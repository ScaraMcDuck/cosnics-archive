<?php
require_once dirname(__FILE__) . '/repository_model_object.class.php';

class LearningObjectMetadata extends RepositoryModelObject
{
	const CLASS_NAME = __CLASS__;
	
	const PROPERTY_LEARNING_OBJECT = 'learning_object';
	const PROPERTY_OVERRIDE_ID     = 'override_id';
	const PROPERTY_TYPE            = 'type';
	const PROPERTY_PROPERTY        = 'property';
	const PROPERTY_VALUE           = 'value';
	
	function LearningObjectMetadata($id = null, $defaultProperties = array ())
	{
	    parent :: __construct($id, $defaultProperties);
	}
	
	/*************************************************************************/
	
	function set_learning_object_id($id)
	{
	    if(isset($id) && is_numeric($id))
	    {
	        $this->set_default_property(self :: PROPERTY_LEARNING_OBJECT, $id);
	    }
	}
	
	function get_learning_object_id()
	{
	    return $this->defaultProperties[self :: PROPERTY_LEARNING_OBJECT];
	}
	
	/*************************************************************************/
	
	function set_override_id($id)
	{
	    if(isset($id) && is_numeric($id))
	    {
	        $this->set_default_property(self :: PROPERTY_OVERRIDE_ID, $id);
	    }
	}
	
	function get_override_id()
	{
	    return $this->defaultProperties[self :: PROPERTY_OVERRIDE_ID];
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
	
	function set_property($property)
	{
	    if(isset($property) && strlen($property) > 0)
	    {
	        $this->set_default_property(self :: PROPERTY_PROPERTY, $property);
	    }
	}
	
	function get_property()
	{
	    return $this->defaultProperties[self :: PROPERTY_PROPERTY];
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
	
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, 
		                self :: PROPERTY_LEARNING_OBJECT, 
		                self :: PROPERTY_OVERRIDE_ID, 
		                self :: PROPERTY_PROPERTY, 
		                self :: PROPERTY_TYPE, 
		                self :: PROPERTY_VALUE, 
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
	        $this->set_id($dm->get_next_learning_object_metadata_id());
	    }
	    
	    $this->set_creation_date(time());
	    
	    return $dm->create_learning_object_metadata($this);
	}
	
	function update()
	{
	    $id = $this->get_id();
	    if(!isset($id))
	    {
	       throw new Exception('Learning object metadata could not be saved as its identity is not set');
	    }
	    
	    $this->set_modification_date(time());
	    
	    $dm = RepositoryDataManager :: get_instance();
	    $result = $dm->update_learning_object_metadata($this);
	    
	    return $result;
	}
	
	function delete()
	{
	    $dm = RepositoryDataManager :: get_instance();
	    $result = $dm->delete_learning_object_metadata($this);
	    
	    return $result;
	}
	
	/*************************************************************************/
}
?>