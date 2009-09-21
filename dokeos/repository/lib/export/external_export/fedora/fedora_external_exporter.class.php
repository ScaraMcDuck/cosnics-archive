<?php
require_once Path :: get_repository_path() . '/lib/export/external_export/rest_external_exporter.class.php';
require_once Path :: get_plugin_path() . '/webservices/rest/client/rest_client.class.php';

class FedoraExternalExporter extends RestExternalExporter  
{
    const DATASTREAM_LOM_NAME        = 'LOM';
    const DATASTREAM_LO_CONTENT_NAME = 'OBJECT';
    
    private $base_url                = null;
    private $get_uid_rest_path       = null;
    private $post_rest_path          = null;
    
    /*************************************************************************/
    
	protected function FedoraExternalExporter($fedora_repository_id = DataClass :: NO_UID) 
	{
		parent :: RestExternalExporter($fedora_repository_id);
	}
	
	
	/*************************************************************************/
	
	/**
	 * (non-PHPdoc)
	 * @see dokeos/common/external_export/BaseExternalExporter#export($learning_object)
	 */
	public function export($learning_object)
	{
	    if($this->check_learning_object_is_exportable($learning_object))
	    {
	        if($this->check_required_metadata($learning_object))
	        {
        	    $this->prepare_export($learning_object);
        	    
        	    /*
        	     * Check if the object exists already in Fedora
        	     * - if not, create it
        	     */
        	    if($this->check_object_exists($learning_object))
        	    {
        	        /*
        	         * Create/Update the LOM-XML datastream in Fedora
        	         */
        	        if($this->save_lom_datastream($learning_object))
        	        {
        	            /*
        	             * Create/Update the learning object datastream in Fedora
        	             */
        	            if($this->save_learning_object_datastream($learning_object))
        	            {
        	                return true;
        	            }
        	            else
        	            {
        	                throw new Exception('The learning object could not be saved in Fedora');
        	            }
        	        }
        	        else
        	        {
        	            throw new Exception('The LOM metadata could not be saved in Fedora');
        	        }
        	    }
        	    else
        	    {
        	        throw new Exception('The object does not exist in Fedora and could not be created');
        	    }
	        }
	        else
	        {	            
	            Redirect :: url(array(Application :: PARAM_APPLICATION => RepositoryManager :: APPLICATION_NAME, Application :: PARAM_ACTION => RepositoryManager :: ACTION_EXTERNAL_REPOSITORY_METADATA_REVIEW, RepositoryManagerExternalRepositoryExportComponent :: PARAM_EXPORT_ID => $this->get_external_export()->get_id(), RepositoryManager :: PARAM_LEARNING_OBJECT_ID => $learning_object->get_id()));
	        }
	    }
	    else
	    {
	        throw new Exception('This object type can not be exported');
	    }
	}
	
	
	/**
	 * Check if an object named with the object's repository uid already exists in Fedora.
	 * If not, the object is created in Fedora  
	 * 
	 * @param $learning_object LearningObject
	 * @return boolean
	 */
	protected function check_object_exists($learning_object)
	{
	    $object_id = $this->get_existing_repository_uid($learning_object);
	    //debug($object_id);
	    
	    /*
	     * Search the object
	     */
	    $search_path = $this->get_full_find_object_rest_path();
	    $search_path = str_replace('{pid}', $object_id, $search_path);
	    $response_document = $this->get_rest_xml_response($search_path, 'get');
	    if(isset($response_document))
	    {
	        /*
	         * Check in the XML if the object exists
	         */
	        $xpath = new DOMXPath($response_document);
	        $xpath->registerNamespace('fedora', 'http://www.fedora.info/definitions/1/0/types/');
	        
    	    $node_list = $xpath->query('/fedora:result/fedora:resultList/fedora:objectFields/fedora:pid');
    	    
    	    if($node_list->length > 0 && $node_list->item(0)->nodeValue == $object_id)
    	    {
    	        return true;
    	    }
	        else
	        {
        	    /*
        	     * Create the object
        	     */
        	    $ingest_path = $this->get_full_ingest_rest_path();
        	    $ingest_path = str_replace('{pid}', $object_id, $ingest_path);
        	    
        	    $response_document = $this->get_rest_xml_response($ingest_path, 'post');
        	    
        	    if(isset($response_document))
        	    {
        	        return true;
        	    }
        	    else
        	    {
        	        return false;
    	    }
	        }
	    }
	    else
	    {
	        throw new Exception('Unable to check if the object already exists in Fedora');
	    }
	}
	
	
	/**
	 * Create or update the LOM datastream in Fedora with the LOM-XML of the object 
	 * @param $learning_object LearningObject
	 * @return boolean Indicates wether the LOM datastream could be saved in Fedora
	 */
	protected function save_lom_datastream($learning_object)
	{
	    $lom_xml     = $this->get_learning_object_metadata_xml($learning_object);
	    $object_id   = $this->get_existing_repository_uid($learning_object);
	    
	    $add_ds_path = $this->get_full_add_datastream_rest_path();
	    
	    $add_ds_path = str_replace('{pid}', $object_id, $add_ds_path);
	    $add_ds_path = str_replace('{dsID}', self :: DATASTREAM_LOM_NAME, $add_ds_path);
	    $add_ds_path = str_replace('{dsLabel}', self :: DATASTREAM_LOM_NAME, $add_ds_path);
	    $add_ds_path = str_replace('{controlGroup}', 'X', $add_ds_path);
	    $add_ds_path = str_replace('{mimeType}', 'text/xml', $add_ds_path);
	    
	    $data_to_send            = array();
	    $data_to_send['content'] = $lom_xml;
	    $data_to_send['mime']    = 'text/xml';
	    
	    $response_document = $this->get_rest_xml_response($add_ds_path, 'post', $data_to_send);
	    
	    //TODO: check what can be a bad response
	    if(isset($response_document))
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	
	/**
	 * 
	 * @param $learning_object
	 * @return boolean
	 */
	protected function save_learning_object_datastream($learning_object)
	{
	    $data_to_send = $this->get_learning_object_content($learning_object);
	    
	    $object_id   = $this->get_existing_repository_uid($learning_object);
	    
	    $add_ds_path = $this->get_full_add_datastream_rest_path();
	    $add_ds_path = str_replace('{pid}', $object_id, $add_ds_path);
	    $add_ds_path = str_replace('{dsID}', self :: DATASTREAM_LO_CONTENT_NAME, $add_ds_path);
	    $add_ds_path = str_replace('{dsLabel}', self :: DATASTREAM_LO_CONTENT_NAME, $add_ds_path);
	    $add_ds_path = str_replace('{controlGroup}', 'M', $add_ds_path);
	    
	    if(isset($data_to_send['file']) && is_array($data_to_send['file']))
	    {
	        $keys = array_keys($data_to_send['file']);
	        if(count($keys) > 0)
	        {
	            $path_to_file = $data_to_send['file'][$keys[0]];
	            $mime_type = $this->get_file_mimetype($path_to_file);

	            if(isset($mime_type) && strlen($mime_type) > 0)
	            {
	                $add_ds_path = str_replace('{mimeType}', $mime_type, $add_ds_path);
	            }
	            else
	            {
	                /*
        	         * delete mimeType from URL
        	         */
        	        $add_ds_path = str_replace('&mimeType={mimeType}', '', $add_ds_path);
	            }
	        }
	    }
	    else
	    {
	        /*
	         * delete mimeType from URL
	         */
	        $add_ds_path = str_replace('&mimeType={mimeType}', '', $add_ds_path);
	    }
	    
	    $response_document = $this->get_rest_xml_response($add_ds_path, 'post', $data_to_send);
	    
	    //TODO: check what can be a bad response
	    if(isset($response_document))
	    {
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
	
	/**
	 * Check if the learning object type export is implemented
	 * 
	 * @param $learning_object LearningObject
	 * @return boolean
	 */
	protected function check_learning_object_is_exportable($learning_object)
	{
	    $type = strtolower($learning_object->get_type());
	    
	    switch($type)
	    {
	        case 'document':
	            return true;
	            
	        default:
	            return false;
	    }
	}
	
	
	/**
	 * Get the learning object content to export
	 * 
	 * @param $learning_object LearningObject
	 * @return unknown_type
	 */
	protected function get_learning_object_content($learning_object)
	{
	    $type = strtolower($learning_object->get_type());
	    
	    $content = null;
	    switch($type)
	    {
	        case 'document':
	            return $this->get_document_content($learning_object);
	            break;
	    }
	}
	
	
	/**
	 * Get the learning object content to send for the type 'Document' 
	 * 
	 * @param $learning_object Document
	 * @return unknown_type
	 */
	protected function get_document_content($learning_object)
	{
	    $data_to_send         = array();
	    $data_to_send['file'] = array(basename($learning_object->get_full_path()) => '@' . $learning_object->get_full_path());

	    return $data_to_send;
	}
	
	
	/*************************************************************************/
	
	/**
	 * @return string
	 */
	public function get_full_find_object_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_find_object_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
	
	/**
	 * @return string
	 */
	public function get_full_add_datastream_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_add_datastream_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}

	
	/**
	 * @return string
	 */
	public function get_full_ingest_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_ingest_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	}
	
	
	/**
	 * @return string
	 */
	public function get_full_get_uid_rest_path()
	{
	    $external_export = $this->get_external_export();
	    
	    if(isset($external_export) && is_a($external_export, 'ExternalExportFedora'))
	    {
	        return $external_export->get_full_get_uid_rest_path();
	    }
	    else
	    {
	        return null;
	    }
	} 
	
	/**
	 * Returns a new UID generated by a Fedora Repository by using an URL allowing to get it through REST
	 * 
	 * @return mixed A new UID generated by a Fedora repository or false if not URL to retrieve a new UID is set in the configuration
	 * @see dokeos/common/external_export/BaseExternalExporter#get_repository_new_uid()
	 */
	public function get_repository_new_uid()
	{ 
	    $response_document = $this->get_rest_xml_response($this->get_full_get_uid_rest_path(), 'post');
	    
	    if(isset($response_document))
	    {
    		/*
    	     * Find the new uid in the XML
    	     */
    	    $xpath = new DOMXPath($response_document);
    	    $node_list = $xpath->query('/pidList/pid');
    	    if($node_list->length > 0)
    	    {
    	        $new_uid = $node_list->item(0)->nodeValue;
    	        
    	        return $new_uid;
    	    }
    	    else
    	    {
    	        throw new Exception('A new uid could not be retrieved from the Fedora repository');
    	    }
	    }
	    else
	    {
	        throw new Exception('A new uid could not be retrieved from the Fedora repository');
	    }
	}

	
}
?>