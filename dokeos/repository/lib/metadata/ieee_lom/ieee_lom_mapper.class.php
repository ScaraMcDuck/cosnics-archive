<?php
require_once dirname(__FILE__) . '/../metadata_mapper.class.php';
require_once dirname(__FILE__) . '/langstring_mapper.class.php';
require_once dirname(__FILE__) . '/ieee_lom_default_metadata_generator.class.php';
require_once ('File/Contact_Vcard_Parse.php');
require_once ('File/Contact_Vcard_Build.php');

class IeeeLomMapper extends MetadataMapper
{
    const METADATA_ID_CATALOG_ATTRIBUTE    = 'catalog_metadata_id';
    const METADATA_ID_LANG_ATTRIBUTE       = 'lang_metadata_id';
    const METADATA_ID_ENTRY_ATTRIBUTE      = 'entry_metadata_id';
    
    private $ieeeLom;
    
    /*
     * Useful to store the new generated id when a new element is saved that 
     * will then be set as constant on the Lom form
     */
    private $constant_values;
    
    /**
     * 
     * @param mixed $learning_object Id of a learning_object or a learning_object instance
     */
	function IeeeLomMapper($learning_object) 
	{
		parent :: MetadataMapper($learning_object);
	}
	
	
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	
	function set_ieee_lom($ieeeLom)
	{
	    $this->ieeeLom = $ieeeLom;
	}
	
	function get_ieee_lom()
	{
	    return $this->ieeeLom;
	}
	
	function get_metadata()
	{
	    /*
	     * Get the IeeeLom object with default values from the datasource
	     */
	    $generator = new IeeeLomDefaultMetadataGenerator();
		$generator->set_learning_object($this->learning_object);
		$this->ieeeLom = $generator->generate();
	    
		/*
		 * Add technical datasource infos to the ieeeLom object to allow 
		 * adding /merge of additional metadata  
		 */
		$this->decorate_document_with_learning_object_id($this->ieeeLom, $this->learning_object->get_id());
		
		/*
		 * Add the metadata defined in the additional metadata datasource table 
		 */
	    $this->additional_metadata_array = $this->retrieve_learning_object_additional_metadata($this->learning_object);
	    $this->merge_additional_metadata($this->additional_metadata_array);

	    return $this->ieeeLom;
	}
	
	function export_metadata($format_for_html_page = false)
	{
	    $ieeeLom = $this->get_metadata();
	    
	    $xsl = new DOMDocument;
		$xsl->load(dirname(__FILE__).'/lom_export.xsl');
		
		$proc = new XSLTProcessor;
		$proc->importStylesheet($xsl);
		
		if($format_for_html_page)
		{
		    echo htmlspecialchars($proc->transformToXML($ieeeLom->get_dom()));
		}
		else
		{
    		echo $proc->transformToXML($ieeeLom->get_dom());
		}
	}
	
	
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	
	public function decorate_document_with_learning_object_id($ieeeLom, $learning_object_id)
    {
        $this->decorate_general_identifier($ieeeLom, $learning_object_id);
        $this->decorate_general_title($ieeeLom, $learning_object_id);
    }
    
    public function decorate_general_identifier($ieeeLom, $learning_object_id)
    {
        $nodes = $ieeeLom->get_identifier();
        foreach ($nodes as $node) 
	    {
	        $node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $learning_object_id);
	    }
    }
	
    public function decorate_general_title($ieeeLom, $learning_object_id)
    {
        $title_nodes = $ieeeLom->get_titles();
        foreach ($title_nodes as $title) 
	    {
	        $title->parentNode->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $learning_object_id);
	    }
    }
    
    
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	
    public function merge_additional_metadata($additional_metadata)
    {
        if(isset($additional_metadata) && is_array($additional_metadata) && count($additional_metadata) > 0)
        {
            $this->additional_metadata = $additional_metadata;    
            $this->merge_general();
            
            //TODO: add IeeeLom sections here
            //e.g. $this->merge_lifeCycle(); 
        }
    }
    
    
    private function merge_general()
	{
	    $this->merge_general_identifier();
	    $this->merge_general_title();
	}
	
    
	//1.2 Identifier-----------------------------------------------------------
	private function merge_general_identifier()
	{
	    $metadata_array = $this->get_additional_metadata(MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER . '[');

	    //debug($metadata_array);
	    
	    $datas = array();
	    
	    foreach ($metadata_array as $learning_object_metadata) 
	    {
	        $metadata_id = $learning_object_metadata->get_id();
	        $index       = StringTool :: get_value_between_chars($learning_object_metadata->get_property());
	    	$concern     = StringTool :: get_value_between_chars($learning_object_metadata->get_property(), 1);
	        $value       = $learning_object_metadata->get_value();
	    	$override_id = $learning_object_metadata->get_override_id();
	    	
	    	if(!isset($datas[$index]))
	        {
	            $datas[$index] = array();
	        }
	        
	        if(!isset($datas[$index][$concern]))
	        {
	            $datas[$index][$concern] = array();
	        }
	        
	        if(isset($override_id))
	    	{
	    	    /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
	    	    $xpath = new DOMXPath($this->ieeeLom->get_dom());
	    	    $original_nodes = $xpath->query('/lom/general/identifier[@'. self :: ORIGINAL_ID_ATTRIBUTE . '=' . $override_id . ']');
	    	    if($original_nodes->length > 0)
	    	    {
	    	        foreach ($original_nodes as $original_node) 
	    	        {
	    	        	$original_node->parentNode->removeChild($original_node);
	    	        }
	    	    }
	    	}
	    	
	    	$datas[$index][$concern]['value'] = $value;
	        $datas[$index][$concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
	        $datas[$index][$concern][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
	    }
	    ksort($datas);
	    
	    foreach ($datas as $data) 
	    {
	        //debug($data);
	        
	        $catalog = isset($data['catalog']['value'])   ? $data['catalog']['value']   : '';
	        $catalog_metadata_id = isset($data['catalog'][self :: METADATA_ID_ATTRIBUTE])   ? $data['catalog'][self :: METADATA_ID_ATTRIBUTE]   : '';
	        
	        $entry   = isset($data['entry']['value']) ? $data['entry']['value'] : '';
	        $entry_metadata_id   = isset($data['entry'][self :: METADATA_ID_ATTRIBUTE]) ? $data['entry'][self :: METADATA_ID_ATTRIBUTE] : '';
	        
	        $catalog_override_id = isset($data['catalog'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $data['catalog'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] : '';
	        $entry_override_id = isset($data['entry'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $data['entry'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] : '';
	        
	        //debug($string_override_id);
	        //debug($lang_override_id);
	        
	        //$langstring = new LangString($string, $lang);
	    	//$this->add_general_title($langstring, $string_metadata_id, $lang_metadata_id, $string_override_id, $lang_override_id);
	    	
	        $this->add_general_identifier($catalog, $entry, $catalog_metadata_id, $entry_metadata_id, $catalog_override_id, $entry_override_id);
	    }
	    
	    //debug($this->get_dom(true));
	}
	
	public function add_general_identifier($catalog, $entry, $catalog_metadata_id = RepositoryModelObject :: NO_UID, $entry_metadata_id = RepositoryModelObject :: NO_UID, $catalog_override_id = RepositoryModelObject :: NO_UID, $entry_override_id = RepositoryModelObject :: NO_UID)
    {
        $new_node = $this->ieeeLom->add_identifier($catalog, $entry);
        
        if(isset($new_node))
        {
            $new_node->setAttribute(self :: METADATA_ID_CATALOG_ATTRIBUTE, $catalog_metadata_id);
            $new_node->setAttribute(self :: METADATA_ID_ENTRY_ATTRIBUTE, $entry_metadata_id);
            $new_node->setAttribute(self :: METADATA_OVERRIDE_ID, $catalog_override_id);
        }
        
        return $new_node;
    }
    
    public function get_identifier()
    {
        $identifier_nodes = $this->ieeeLom->get_identifier();
        
        //debug($identifier_nodes);
        
        $identifiers = array();
        foreach ($identifier_nodes as $identifier) 
	    {
	        $catalog = XMLTool :: get_first_element_value_by_tag_name($identifier, IeeeLom :: CATALOG);
	        $catalog_metadata_id = null;
	        $entry   = XMLTool :: get_first_element_value_by_tag_name($identifier, IeeeLom :: ENTRY);
	        $entry_metadata_id = null;
	        $original_id = null;
	        
	        if($identifier->hasAttribute(self :: METADATA_ID_CATALOG_ATTRIBUTE))
	        {
	            $catalog_metadata_id = $identifier->getAttribute(self :: METADATA_ID_CATALOG_ATTRIBUTE);
	        }
	        
	        if($identifier->hasAttribute(self :: METADATA_ID_ENTRY_ATTRIBUTE))
	        {
	            $entry_metadata_id = $identifier->getAttribute(self :: METADATA_ID_ENTRY_ATTRIBUTE);
	        }
	        
	        if($identifier->hasAttribute(self :: ORIGINAL_ID_ATTRIBUTE))
	        {
	            $original_id = $identifier->getAttribute(self :: ORIGINAL_ID_ATTRIBUTE);
	        }
	        
//	        debug($identifier);
//	        debug($catalog);
//	        debug($entry);
	        
	        $ident_array = array();
	        $ident_array[IeeeLom :: CATALOG]                      = $catalog;
	        $ident_array[IeeeLom :: ENTRY]                        = $entry;
	        $ident_array[self :: METADATA_ID_CATALOG_ATTRIBUTE]   = $catalog_metadata_id;
	        $ident_array[self :: METADATA_ID_ENTRY_ATTRIBUTE]     = $entry_metadata_id;
	        $ident_array[self :: ORIGINAL_ID_ATTRIBUTE]           = $original_id;
	        
	        $identifiers[] = $ident_array;
	    }
	    
	    //debug($identifiers);
	     
	    return $identifiers;
    }
	
	
	//1.2 Title-----------------------------------------------------------
    private function merge_general_title()
    {
	    $metadata_array = $this->get_additional_metadata(MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[');
	    //debug($metadata_array, 'Additional Metadata');
	    
	    $titles = array();
	    
	    foreach ($metadata_array as $learning_object_metadata) 
	    {
	        $metadata_id = $learning_object_metadata->get_id();
	        $index       = StringTool :: get_value_between_chars($learning_object_metadata->get_property());
	    	$concern     = StringTool :: get_value_between_chars($learning_object_metadata->get_property(), 1);
	        $value       = $learning_object_metadata->get_value();
	    	$override_id = $learning_object_metadata->get_override_id();
	    	
	        if(!isset($titles[$index]))
	        {
	            $titles[$index] = array();
	        }
	        
	        if(!isset($titles[$index][$concern]))
	        {
	            $titles[$index][$concern] = array();
	        }
	        
	        if(isset($override_id))
	    	{
	    	    /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
	    	    $xpath = new DOMXPath($this->ieeeLom->get_dom());
	    	    $original_nodes = $xpath->query('/lom/general/title[@'. self :: ORIGINAL_ID_ATTRIBUTE . '=' . $override_id . ']');
	    	    if($original_nodes->length > 0)
	    	    {
	    	        foreach ($original_nodes as $original_node) 
	    	        {
	    	        	$original_node->parentNode->removeChild($original_node);
	    	        }
	    	    }
	    	}
	    	
	        $titles[$index][$concern]['value'] = $value;
	        $titles[$index][$concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
	        $titles[$index][$concern][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
	    }
	    ksort($titles);
	    
	    //debug($titles);
	    
	    foreach ($titles as $title) 
	    {
	        //debug($title);
	        
	        $string = isset($title['string']['value'])   ? $title['string']['value']   : '';
	        $string_metadata_id = isset($title['string'][self :: METADATA_ID_ATTRIBUTE])   ? $title['string'][self :: METADATA_ID_ATTRIBUTE]   : '';
	        
	        $lang   = isset($title['language']['value']) ? $title['language']['value'] : '';
	        $lang_metadata_id   = isset($title['language'][self :: METADATA_ID_ATTRIBUTE]) ? $title['language'][self :: METADATA_ID_ATTRIBUTE] : '';
	        
	        $string_override_id = isset($title['string'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $title['string'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] : '';
	        $lang_override_id = isset($title['language'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID]) ? $title['language'][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] : '';
	        
	        //debug($string_override_id);
	        //debug($lang_override_id);
	        
	        $langstring = new LangString($string, $lang);
	    	$this->add_general_title($langstring, $string_metadata_id, $lang_metadata_id, $string_override_id, $lang_override_id);
	    }
	    
	    //debug($this->ieeeLom->get_dom());
	}
	
    public function add_general_title($langstring, $string_metadata_id, $lang_metadata_id, $string_override_id, $lang_override_id)
    {
        $new_node = $this->ieeeLom->add_title($langstring);
        
        if(isset($new_node))
        {
            $new_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $string_metadata_id);
            $new_node->setAttribute(self :: METADATA_ID_LANG_ATTRIBUTE, $lang_metadata_id);
            $new_node->setAttribute(LangStringMapper :: STRING_OVERRIDE_ID, $string_override_id);
            $new_node->setAttribute(LangStringMapper :: LANGUAGE_OVERRIDE_ID, $lang_override_id);
        }
        
        //debug($new_node);
        
        return $new_node;
    }
    
    public function get_titles()
    {
        $title_nodes = $this->ieeeLom->get_titles();
        
        //debug($title_nodes, '$title_nodes');
        
        $langstrings = new LangStringMapper();
        
        foreach ($title_nodes as $title)
	    {
	        //debug($title->parentNode, '$title_nodes->parentNode');
	        
	        $langstrings->add_string($title->nodeValue, 
	                                 $title->getAttribute('language'), 
	                                 $title->parentNode->getAttribute(self :: METADATA_ID_ATTRIBUTE), 
	                                 $title->parentNode->getAttribute(self :: METADATA_ID_LANG_ATTRIBUTE),
	                                 $title->parentNode->getAttribute(LangStringMapper :: STRING_OVERRIDE_ID),
	                                 $title->parentNode->getAttribute(LangStringMapper :: LANGUAGE_OVERRIDE_ID),
	                                 $title->parentNode->getAttribute(self :: ORIGINAL_ID_ATTRIBUTE));
	    }
	    
	    return $langstrings;
    }
	
	
    //2.3 Contribution-----------------------------------------------------------
//    public function get_contribution()
//    {
//        $contribution_nodes = $this->ieeeLom->get_contribute();
//        
//        //debug($contribution_nodes);
//        
//        $contributions = array();
//        foreach ($contribution_nodes as $contribution) 
//	    {
//	        $role     = $this->get_first_element_value_by_xpath($contribution, '/contribute/role/value');
//	        $entities = $this->get_all_values_by_xpath($contribution, '/contribute/entity');
//	        $date     = $this->get_first_element_value_by_xpath($contribution, '/contribute/date');
//	        
//	        $vcards = array();
//	        foreach($entities as $entity)
//	        {
//	            //debug($entity);
//	            
//	            $vcard_parser = new Contact_Vcard_Parse();
//	            $vcard_array  = $vcard_parser->fromText($entity);
//	            
//	            //debug($vcard_array);
//	            
//	            $vcard        = new Contact_Vcard_Build();
//	            $vcard->setFromArray($vcard_array[0]);
//	            
//	            $vcards[] = $vcard;
//	        }
//	        
//	        $contribution_array             = array();
//	        $contribution_array['role']     = $role;
//	        $contribution_array['entity']   = $vcards;
//	        $contribution_array['date']     = $date;
//	        
//	        $contributions[] = $contribution_array;
//	    }
//	    
//	    //debug($contributions);
//        
//        return $contributions;
//    }
    
    
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	
    /**
     * Save the values submitted by a form in the datasource 
     * 
     * @param $submitted_values Array of submitted values
     */
    public function save_submitted_values($submitted_values)
    {
        //debug($submitted_values);
        
        $this->constant_values = array();
        
        $this->save_general_identifier($submitted_values);
        $this->save_general_title($submitted_values);
        //$this->save_lifeCycle_contribution($submitted_values);
        
        return (count($this->errors) == 0);
    }
    
    
    //1.2 Identifier-----------------------------------------------------------
    private function save_general_identifier($submitted_values)
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(LearningObjectMetadata :: PROPERTY_PROPERTY, MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER . '[', IeeeLom :: VERSION);
        
        $saved_metadata_id    = array();
        
        if(isset($submitted_values[MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER]))
        {
            foreach ($submitted_values[MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER] as $key => $data) 
            {
                //debug($data);
                $meta_data = $this->get_new_learning_object_metadata($data[self :: METADATA_ID_CATALOG_ATTRIBUTE], IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][catalog]', $data['catalog'], $data[self :: ORIGINAL_ID_ATTRIBUTE], IeeeLom :: VERSION);

                try
            	{
            	    $meta_data->save();
            	    $saved_metadata_id[$meta_data->get_id()] = $meta_data;
            	    
            	    /*
            	     * Set the new generated ID in the form 
            	     */
            	    $input_name = MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][' . self :: METADATA_ID_CATALOG_ATTRIBUTE . ']';
            	    $this->store_constant_value($input_name, $meta_data->get_id());
            	    
            	    $meta_data = $this->get_new_learning_object_metadata($data[self :: METADATA_ID_ENTRY_ATTRIBUTE], IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][entry]', $data['entry'], null, IeeeLom :: VERSION);
            	    
            	    try 
            	    {
            	    	$meta_data->save();
            	    	$saved_metadata_id[$meta_data->get_id()] = $meta_data;
            	    	
            	    	/*
                	     * Set the new generated ID in the form 
                	     */
                	    $input_name = MetadataLOMEditForm :: LOM_GENERAL_IDENTIFIER . '[' . $key . '][' . self :: METADATA_ID_ENTRY_ATTRIBUTE . ']';
                	    $this->store_constant_value($input_name, $meta_data->get_id());
            	    } 
            	    catch (Exception $ex) 
            	    {
            	         $this->add_error($ex->getMessage());
            	    }
            	}
            	catch(Exception $ex)
            	{
            	     $this->add_error($ex->getMessage());
            	}
            }
        }
        
        /*
         * Delete metadata that were not sent back for saving
         */
        $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
    }
    
    //1.2 Title----------------------------------------------------------------
    private function save_general_title($submitted_values)
    {
        $existing_metadata_id = $this->retrieve_existing_metadata(LearningObjectMetadata :: PROPERTY_PROPERTY, MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[');
        
        //debug($existing_metadata_id);
        
        $saved_metadata_id = array();
        
        if(isset($submitted_values[MetadataLOMEditForm :: LOM_GENERAL_TITLE]))
        {
            //debug($submitted_values[MetadataLOMEditForm :: LOM_GENERAL_TITLE], 'Submitted general title values');
            
            foreach ($submitted_values[MetadataLOMEditForm :: LOM_GENERAL_TITLE] as $key => $title) 
            {
            	$meta_data = $this->get_new_learning_object_metadata($title['string_metadata_id'], IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[' . $key . '][string]', $title['string'], $title['string_original_id'], IeeeLom :: VERSION);
            	
            	//debug($metaData->get_default_properties(), 'LearningObjectMetadata in mapper');
            	
            	try
            	{
            	    $meta_data->save();
            	    $saved_metadata_id[$meta_data->get_id()] = $meta_data;
            	    
            	    /*
            	     * Set the new generated ID in the form 
            	     */
            	    $input_name = MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[' . $key . '][string_metadata_id]';
            	    $this->store_constant_value($input_name, $meta_data->get_id());
            	}
            	catch(Exception $ex)
            	{
            	    $this->add_error($ex->getMessage());
            	}
            	
            	if(isset($title['language']) && strlen($title['language']) > 0)
            	{
            	    if($title['language'] == '0')
            	    {
            	        $title['language'] = IeeeLom :: NO_LANGUAGE;
            	    }
            	    
            	    $meta_data = $this->get_new_learning_object_metadata($title['language_metadata_id'], IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[' . $key . '][language]', $title['language'], IeeeLom :: VERSION);
            	   
                	try
                	{ 
                	    $meta_data->save();
                	    $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                	    
                	    /*
                	     * Set the new generated ID in the form 
                	     */
                	    $input_name = MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[' . $key . '][language_metadata_id]';
                	    $this->store_constant_value($input_name, $meta_data->get_id());
                	}
                	catch(Exception $ex)
                	{
                	    $this->add_error($ex->getMessage());
                	}
            	}
            }
            
            /*
             * Delete metadata that were not sent back for saving
             */
            $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
        }
    }
    
    
    //2.3 Contribution---------------------------------------------------------
//    private function save_lifeCycle_contribution($submitted_values)
//    {
//        $existing_metadata = $this->retrieve_existing_metadata(LearningObjectMetadata :: PROPERTY_PROPERTY, MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[');
//        
//        $existing_metadata_id = array();
//        $saved_metadata_id    = array();
//        
//        while ($ex_meta = $existing_metadata->next_result())
//        {
//        	$existing_metadata_id[$ex_meta->get_id()] = $ex_meta;
//        }
//        
//        //debug($existing_metadata);
//        
//        if(isset($submitted_values[MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION]))
//        {
//             debug($submitted_values[MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION]);
//             
//             foreach ($submitted_values[MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION] as $key => $contribution) 
//             {
//                 try
//                 {
//                     /*
//                      * Save Role
//                      */
//                     $meta_data = $this->get_new_learning_object_metadata(null, IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role]', $contribution['role'], null);
//                     $meta_data->save();
//                     $saved_metadata_id[$meta_data->get_id()] = $meta_data;
//                 
//                     /*
//            	     * Set the new generated ID in the form 
//            	     */
//                     $input_name = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role_id]';
//                     $this->store_constant_value($input_name, $meta_data->get_id());
//                     
//                     try
//                     {
//                         /*
//                          * Save Date
//                          */
//                         $meta_data = $this->get_new_learning_object_metadata(null, IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date]', $contribution['date'], null);
//                         $meta_data->save();
//                         $saved_metadata_id[$meta_data->get_id()] = $meta_data;
//                     
//                         /*
//                	     * Set the new generated ID in the form 
//                	     */
//                         $input_name = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date_id]';
//                         $this->store_constant_value($input_name, $meta_data->get_id());
//                         
//                         try
//                         {
//                             /*
//                              * Save Entity-ies
//                              */
//                             
//                             
//                         }
//                         catch(Exception $ex)
//                         {
//                              $this->add_error($ex->getMessage());
//                         }
//                     }
//                     catch(Exception $ex)
//                     {
//                          $this->add_error($ex->getMessage());
//                     }
//                 }
//                 catch(Exception $ex)
//                 {
//                      $this->add_error($ex->getMessage());
//                 }
//             }            	
//        }
//    }
    
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	
    private function store_constant_value($input_name, $input_value)
    {
        $this->constant_values[] = array('name' => $input_name, 'value' => $input_value);
    }
    
    public function get_constant_values()
    {
        return $this->constant_values;
    }
    
    
	/****************************************************************************************/
	/****************************************************************************************/
	/****************************************************************************************/
	
	
}
?>