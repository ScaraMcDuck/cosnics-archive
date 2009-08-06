<?php
require_once dirname(__FILE__) . '/../learning_object_metadata.class.php';
require_once Path :: get_common_path() . 'xml/xml_tool.class.php';
require_once Path :: get_common_path() . 'string/string_tool.class.php';

abstract class MetadataMapper 
{
    const ORIGINAL_ID_ATTRIBUTE            = 'original_id';
    const METADATA_ID_ATTRIBUTE            = 'metadata_id'; 
    const METADATA_OVERRIDE_ID             = LearningObjectMetadata :: PROPERTY_OVERRIDE_ID;
    
    protected $learning_object;
    protected $additional_metadata_array;
    protected $repository_data_manager;
    protected $errors;
    
    /**
     * 
     * @param mixed LearningObject id or a LearningObject instance
     */
	function MetadataMapper($learning_object) 
	{
	    $this->errors = array();
	    
	    if(isset($learning_object))
		{
		    $this->repository_data_manager = RepositoryDataManager :: get_instance();
		    
		    if(is_numeric($learning_object))
		    {
		        $lo = $this->repository_data_manager->retrieve_learning_object($learning_object);
		        if(isset($lo))
		        {
		            $this->learning_object = $lo;
		        }
		        else
		        {
		            throw new Exception('learning_object could not be retrieved while creating an IeeeLomMapper');
		        }
		    }
		    elseif(is_a($learning_object, 'LearningObject'))
		    {
		        $this->learning_object = $learning_object;
		    }
		    else
		    {
		        throw new Exception('Not able to create MetadataMapper. Wrong type parameters.');
		    }
		}
		else
		{
		    throw new Exception('Unable to create a MetadataMapper without any learning_object');
		}
	}
	
	
	/****************************************************************************************/
	
	/**
	 * Get the metadata from the metadata table for a specific learning object
	 * 
	 * @param LearningObject $learning_object
	 * @return array of LearningObjectMetadata
	 */
	protected function retrieve_learning_object_additional_metadata($learning_object)
	{
	    $id = $learning_object->get_id(); 
	    $conditions = new EqualityCondition(LearningObjectMetadata :: PROPERTY_LEARNING_OBJECT, $id);
	    
	    $additional_metadata = $this->repository_data_manager->retrieve_learning_object_metadata($conditions, null, null, null, null);
	    $additional_metadata_array = array();
	    while ($metadata = $additional_metadata->next_result())
        {
            $additional_metadata_array[] = $metadata;            
        }
            
	    return $additional_metadata_array;
	}
	
	/**
	 * Get existing metadata from the metadata table for a specific metadata field and type
	 * 
	 * @param $field_name The name of the datasource column (e.g. 'property')
	 * @param $start_like The beginning of the metadata field name (e.g. 'general_title[')
	 * @param $metadata_type The type of Metadata (e.g. 'LOMV1.0')
	 * @return ObjectResultSet
	 */
	protected function retrieve_existing_metadata($field_name, $start_like, $metadata_type)
    {
        $conditions   = array();
        $conditions[] = new EqualityCondition(LearningObjectMetadata :: PROPERTY_LEARNING_OBJECT, $this->learning_object->get_id());
        $conditions[] = new EqualityCondition(LearningObjectMetadata :: PROPERTY_TYPE, $metadata_type);
        $conditions[] = new LikeCondition($field_name, $start_like);
        $condition    = new AndCondition($conditions);
         
        $existing_metadata = $this->repository_data_manager->retrieve_learning_object_metadata($condition);
        
        return $existing_metadata;
    }
    
    /**
     * Get an array of existing metadata id from the metadata table for a specific metadata field and type
     * 
     * @param $field_name The name of the datasource column (e.g. 'property')
	 * @param $start_like The beginning of the metadata field name (e.g. 'general_title[')
	 * @param $metadata_type The type of Metadata (e.g. 'LOMV1.0')
	 * @return array of id
     */
    protected function retrieve_existing_metadata_id($field_name, $start_like, $metadata_type)
    {
        $existing_metadata = $this->retrieve_existing_metadata($field_name, $start_like, $metadata_type);
        
        $existing_metadata_id = array();
       
        while ($ex_meta = $existing_metadata->next_result())
        {
        	$existing_metadata_id[$ex_meta->get_id()] = $ex_meta;
        }
        
        return $existing_metadata_id;
    }
	
    /**
     * Compare two arrays of ids. For each existing id not saved, delete the existing metadata 
     * record in the datasource  
     * 
     * @param $existing_metadata_id Array of existing metadata ids
     * @param $saved_metadata_id Array of saved metadata id (= the metadata record ids to keep in datasource)
     */
    protected function delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id)
    {
    	/*
         * Remove metadata that were sent back for saving
         */
        //debug($existing_metadata_id);
        //debug($saved_metadata_id);
        
        foreach ($saved_metadata_id as $saved_id => $meta) 
        {
        	if(array_key_exists($saved_id, $existing_metadata_id))
        	{
        	    unset($existing_metadata_id[$saved_id]);
        	}
        }
        
        /*
         * Delete the existing metadata that were not saved
         */
        foreach ($existing_metadata_id as $saved_id => $metadata_to_delete) 
        {
            try
            {
                $metadata_to_delete->delete();
            }
            catch(Exception $ex)
            {
                $this->add_error($ex->getMessage());
            }
        }
    }
    
    /**
     * Filter the already retrieved existing metadata
     * 
     * @param $filter The beginning of the property name to filter on 
     * @return array of LearningObjectMetadata
     */
    protected function get_additional_metadata($filter)
	{
	    if(isset($filter))
	    {
	        $filtered_metadata = array();
	        
	        foreach ($this->additional_metadata_array as $metadata) 
	        {
	        	if(StringTool :: start_with($metadata->get_property(), $filter))
	        	{
	        	    $filtered_metadata[] = $metadata;
	        	}
	        }
	        
	        return $filtered_metadata;
	    }
	}
    
    /**
     * Return the LearningObject instance
     *
     * @return LearningObject
     */
	public function get_learning_object()
    {
        return $this->learning_object;
    }
	
    /**
     * Return a new LearningObjectMetadata instance with the given properties 
     * 
     * @return LearningObjectMetadata
     */
    protected function get_new_learning_object_metadata($id = null, $type = null, $property = null, $value = null, $override_id = null)
    {
//        debug($property);
//        debug($value);
        
        $metaData = new LearningObjectMetadata();
    	$metaData->set_learning_object_id($this->learning_object->get_id());
    	$metaData->set_type($type);
    	
    	if(isset($id))
    	{
    	    $metaData->set_id($id);
    	}
    	
    	if(isset($property))
    	{
    	    $metaData->set_property($property);
    	}
    	
    	$metaData->set_value($value);
    	
    	if(isset($override_id) && strlen($override_id) > 0 && is_numeric($override_id) && $override_id != RepositoryDataClass :: NO_UID)
    	{
    	    $metaData->set_override_id($override_id);
    	}
    	
    	return $metaData;
    }
    
    /**
     * Add an error message to the collection of errors
     *
     * @return $error_msg The new error message
     */
    protected function add_error($error_msg)
    {
        $this->errors[] = array('message' => $error_msg);
    }
    
    /**
     * Indicates wether the collection of errors contains elements
     * 
     * @return bool
     */
    public function has_error()
    {
        return (count($this->errors) > 0);
    }
    
    /**
     * Return an HTML formatted list of errors
     * @return string HTML list
     */
    public function get_errors_as_html()
    {
        if(count($this->errors) > 0)
        {
            $error_str = '<ul>';
            
            foreach ($this->errors as $error) 
            {
            	$error_str .= '<li>' . $error['message'] . '</li>';
            }
            
            $error_str .= '</ul>';
            
            return $error_str;
        }
    }
    
    
    /**
     * Merge the default metadata retrieved form the LearningObject properties
     * with the metadata stored in the metadata table of the datasource
     * 
     * @param $additional_metadata Array of LearningObjectMetadata
     */
	abstract function merge_additional_metadata($additional_metadata);
	
	
	/**
	 * Generates the metadata for the setted LearningObject and return it as an object
	 * 
	 * @return mixed Object 
	 */
	abstract function get_metadata();
	
	/**
	 * Generates the metadata for the setted LearningObject and print it in the page 
	 * 
	 * @param $format_for_html_page bool Indicates wether the printed metadata must be formatted to be included in a HTML page
	 * @return void
	 */
	abstract function export_metadata($format_for_html_page = false);
    
}
?>