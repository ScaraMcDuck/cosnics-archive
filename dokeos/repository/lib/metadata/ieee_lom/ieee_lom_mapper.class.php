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
    const METADATA_ID_ROLE_ATTRIBUTE       = 'role_metadata_id';
    const METADATA_ID_ENTITY_ATTRIBUTE     = 'entity_metadata_id';
    const METADATA_ID_DATE_ATTRIBUTE       = 'date_metadata_id';
    
    
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

	    //debug($this->ieeeLom->get_dom());
	    
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
        
        $this->decorate_lifeCycle_contribution($ieeeLom, $learning_object_id);
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
    
    public function decorate_lifeCycle_contribution($ieeeLom, $learning_object_id)
    {
        $nodes = $ieeeLom->get_contribute();
        
        foreach ($nodes as $node) 
	    {
	        $node->setAttribute('contribution_override_id', $learning_object_id);
	        
	        //role
	        $role_node = XMLTool :: get_first_element_by_tag_name($node, 'role');
	        if(isset($role_node))
	        {
	            $role_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $learning_object_id);
	        }
	        
	        //entity
	        //Note: default entity is only one node -> we set the first
	        $entity_node = XMLTool :: get_first_element_by_tag_name($node, 'entity');
	        if(isset($entity_node))
	        {
	            $entity_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $learning_object_id);
	        }
	        
	        //date
	        $date_node = XMLTool :: get_first_element_by_tag_name($node, 'date');
	        if(isset($date_node))
	        {
	            $date_node->setAttribute(self :: ORIGINAL_ID_ATTRIBUTE, $learning_object_id);
	        }
	    }
	    
	    //debug($nodes);
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
            $this->merge_lifeCycle();
            
            //TODO: add other IeeeLom sections here 
        }
    }
    
    
    private function merge_general()
	{
	    $this->merge_general_identifier();
	    $this->merge_general_title();
	}
	
	private function merge_lifeCycle()
	{
	    $this->merge_lifeCycle_contribution();
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
	        $entry   = XMLTool :: get_first_element_value_by_tag_name($identifier, IeeeLom :: ENTRY);
	        
//	        debug($identifier);
//	        debug($catalog);
//	        debug($entry);
	        
	        $ident_array = array();
	        $ident_array[IeeeLom :: CATALOG]                      = $catalog;
	        $ident_array[IeeeLom :: ENTRY]                        = $entry;
	        $ident_array[self :: METADATA_ID_CATALOG_ATTRIBUTE]   = XMLTool :: get_attribute($identifier, self :: METADATA_ID_CATALOG_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        $ident_array[self :: METADATA_ID_ENTRY_ATTRIBUTE]     = XMLTool :: get_attribute($identifier, self :: METADATA_ID_ENTRY_ATTRIBUTE, RepositoryModelObject :: NO_UID);;
	        $ident_array[self :: ORIGINAL_ID_ATTRIBUTE]           = XMLTool :: get_attribute($identifier, self :: ORIGINAL_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);;;
	        
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
	        //debug($title);
	        
	        $string_metadata_id   = XMLTool :: get_attribute($title->parentNode, self :: METADATA_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        $language_metadata_id = XMLTool :: get_attribute($title->parentNode, self :: METADATA_ID_LANG_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        $string_override_id   = XMLTool :: get_attribute($title->parentNode, LangStringMapper :: STRING_OVERRIDE_ID, RepositoryModelObject :: NO_UID);
	        $language_override_id = XMLTool :: get_attribute($title->parentNode, LangStringMapper :: LANGUAGE_OVERRIDE_ID, RepositoryModelObject :: NO_UID);
	        $string_original_id   = XMLTool :: get_attribute($title->parentNode, self :: ORIGINAL_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        
	        $langstrings->add_string($title->nodeValue, $title->getAttribute('language'), $string_metadata_id, $language_metadata_id, $string_override_id, $language_override_id, $string_original_id);
	    }
	    
	    return $langstrings;
    }
	
	
    //2.3 Contribution-----------------------------------------------------------
    private function merge_lifeCycle_contribution()
    {
        $metadata_array = $this->get_additional_metadata(MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[');
        
        //debug($metadata_array);
        
        $contributions = array();
        
        foreach ($metadata_array as $learning_object_metadata) 
	    {
	        $metadata_id = $learning_object_metadata->get_id();
	        $index       = StringTool :: get_value_between_chars($learning_object_metadata->get_property());
	    	$concern     = StringTool :: get_value_between_chars($learning_object_metadata->get_property(), 1);
	        $value       = $learning_object_metadata->get_value();
	    	$override_id = $learning_object_metadata->get_override_id();
	    	
	    	if(!isset($contributions[$index]))
	        {
	            $contributions[$index] = array();
	        }
	        
	        if(!isset($contributions[$index][$concern]))
	        {
	            $contributions[$index][$concern] = array();
	        }
	        
	        if(isset($override_id))
	    	{
	    	    /*
	    	     * Remove the original node corresponding to the same id 
	    	     */
	    	    
	    	    //debug($this->ieeeLom->get_dom());
	    	    
	    	    $xpath = new DOMXPath($this->ieeeLom->get_dom());
	    	    $original_nodes = $xpath->query('/lom/lifeCycle/contribute[@contribution_override_id=' . $override_id . ']');
	    	    if($original_nodes->length > 0)
	    	    {
	    	        foreach ($original_nodes as $original_node) 
	    	        {
	    	        	$original_node->parentNode->removeChild($original_node);
	    	        }
	    	    }
	    	}
	    	
	    	if($concern == 'entity')
	    	{
	    	    $entity_index = StringTool :: get_value_between_chars($learning_object_metadata->get_property(), 2);
	    	    $entity_concern = StringTool :: get_value_between_chars($learning_object_metadata->get_property(), 3);
	    	    
//	    	    debug($entity_index);
//    	        debug($entity_concern);
    	        
    	        $contributions[$index][$concern][$entity_index][$entity_concern]['value'] = $value;
    	        $contributions[$index][$concern][$entity_index][$entity_concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
    	        $contributions[$index][$concern][$entity_index][$entity_concern][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
	    	}
	    	else
	    	{
	    	    $contributions[$index][$concern]['value'] = $value;
    	        $contributions[$index][$concern][self :: METADATA_ID_ATTRIBUTE] = $metadata_id;
    	        $contributions[$index][$concern][LearningObjectMetadata :: PROPERTY_OVERRIDE_ID] = $override_id;
	    	}
	    }
	    ksort($contributions);
	    
	    //debug($contributions);
        
	    foreach ($contributions as $contribution) 
	    {
	        //debug($contribution['entity']);
	        
	        $this->add_lifeCycle_contribution($contribution['role']['value'], 
	                                            $contribution['role']['metadata_id'], 
	                                            $contribution['role']['override_id'],
	                                            $contribution['entity'],
	                                            $contribution['date']['value'],
	                                            $contribution['date']['metadata_id'],
	                                            $contribution['date']['override_id']);
	    }
    }
    
    public function add_lifeCycle_contribution($role_value, $role_metadata_id, $role_override_id, $entity_values, $date_value, $date_metadata_id, $date_override_id)
    {
        $voc_role = new IeeeLomVocabulary($role_value);
        $lom_dt = new IeeeLomDatetime();
        $lom_dt->set_datetime_from_string($date_value);
        
        $entities = array();
        foreach ($entity_values as $entity) 
        {
        	$vcard = new Contact_Vcard_Build();
        	$vcard->setFormattedName($entity['name']['value']);
        	$vcard->setName($entity['name']['value']);
        	$vcard->addEmail($entity['email']['value']);
        	$vcard->addOrganization($entity['organisation']['value']);
        	
        	$entities[] = $vcard->fetch();
        }
        
        $new_node = $this->ieeeLom->add_contribute($voc_role, $entities, $lom_dt);
        
        if(isset($new_node))
        {
            //debug($new_node);
            
            $role_node = XMLTool :: get_first_element_by_tag_name($new_node, 'role');
            if(isset($role_node))
            {
                $role_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $role_metadata_id);
            }
            
            //debug($new_node->ownerDocument);
            $entity_nodes = XMLTool :: get_all_element_by_xpath($new_node, '/lom/lifeCycle/contribute/entity');
            if(isset($entity_nodes))
            {
                foreach ($entity_nodes as $index => $entity_node) 
                {
                    //debug($entity_values[$index]);
                    $entity_node->setAttribute('name_metadata_id', $entity_values[$index]['name']['metadata_id']);
                    $entity_node->setAttribute('email_metadata_id', $entity_values[$index]['email']['metadata_id']);
                    $entity_node->setAttribute('organisation_metadata_id', $entity_values[$index]['organisation']['metadata_id']);
                }
            }
            
            $date_node = XMLTool :: get_first_element_by_tag_name($new_node,'date');
            if(isset($date_node))
            {
                $date_node->setAttribute(self :: METADATA_ID_ATTRIBUTE, $date_metadata_id);
            }
        }
        
        //debug($new_node);
    }
    
    public function get_lifeCycle_contribution()
    {
        $contribution_nodes = $this->ieeeLom->get_contribute();
        
        //debug($contribution_nodes);
        
        $contributions = array();
        foreach ($contribution_nodes as $contribution) 
	    {
	        //debug($contribution);
	        $contribution_array = array();
	        
	        $role     = XMLTool :: get_first_element_by_xpath($contribution, '/contribute/role');
	        $entities = XMLTool :: get_all_element_by_xpath($contribution, '/lom/lifeCycle/contribute/entity');
	        $date     = XMLTool :: get_first_element_by_xpath($contribution, '/contribute/date');
	        
	        //debug($entities);
	        
	        $contribution_array['contribution_override_id'] = XMLTool :: get_attribute($role, self :: ORIGINAL_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        
	        //$vcards = array();
	        //for($i = 0; $i < $entities->length; $i++)
	        foreach($entities as $entity_index => $entity)
	        {
	            //debug($entity);
	            
	            $vcard_parser = new Contact_Vcard_Parse();
	            $vcard_array  = $vcard_parser->fromText($entity->nodeValue);
	            
	            //debug($vcard_array);
	            
	            $vcard        = new Contact_Vcard_Build();
	            $vcard->setFromArray($vcard_array[0]);
	            
	            $contribution_array['entity'][$entity_index]['name']['value']                               = $vcard->getValue('FN');
	            $contribution_array['entity'][$entity_index]['name']['name_metadata_id']                    = $entity->getAttribute('name_metadata_id');
	            $contribution_array['entity'][$entity_index]['email']['value']                              = $vcard->getValue('EMAIL');
	            $contribution_array['entity'][$entity_index]['email']['email_metadata_id']                  = $entity->getAttribute('email_metadata_id');
	            $contribution_array['entity'][$entity_index]['organisation']['value']                       = $vcard->getValue('ORG');
	            $contribution_array['entity'][$entity_index]['organisation']['organisation_metadata_id']    = $entity->getAttribute('organisation_metadata_id');
	            //$contribution_array['entity'][$entity_index][IeeeLomMapper :: METADATA_ID_ENTITY_ATTRIBUTE] = XMLTool :: get_attribute($entity, self :: METADATA_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	            $contribution_array['entity'][$entity_index]['entity_override_id']                          = XMLTool :: get_attribute($entity, self :: ORIGINAL_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	            
	            //$vcards[] = $vcard;
	        }
	        
	        $contribution_array['role']                             = XMLTool :: get_first_element_value_by_xpath($role, '/role/value');
	        $contribution_array[self :: METADATA_ID_ROLE_ATTRIBUTE] = XMLTool :: get_attribute($role, self :: METADATA_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        $contribution_array['role_override_id']                 = XMLTool :: get_attribute($role, self :: ORIGINAL_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID);
	        
	        $contribution_array['date']                             = $date->nodeValue;
	        $contribution_array[self :: METADATA_ID_DATE_ATTRIBUTE] = XMLTool :: get_attribute($date, self :: METADATA_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID); 
	        $contribution_array['date_override_id']                 = XMLTool :: get_attribute($date, self :: ORIGINAL_ID_ATTRIBUTE, RepositoryModelObject :: NO_UID); 
	        
	        $contributions[] = $contribution_array;
	    }
	    
	    //debug($contributions);
        
        return $contributions;
    }
    
    
    //2.3.2 Contribution Entity
    public function add_lifeCycle_entity($contribute_index, $entity_value)
    {
        $new_node = $this->ieeeLom->add_lifeCycle_entity($contribute_index, $entity_value);
    }
    
    public function remove_lifeCycle_entity($contribute_index, $entity_index)
    {
        $new_node = $this->ieeeLom->remove_lifeCycle_entity($contribute_index, $entity_index);
    }
    
    
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
        $this->save_lifeCycle_contribution($submitted_values);
        
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
        $existing_metadata_id = $this->retrieve_existing_metadata_id(LearningObjectMetadata :: PROPERTY_PROPERTY, MetadataLOMEditForm :: LOM_GENERAL_TITLE . '[', IeeeLom :: VERSION);
        
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
    private function save_lifeCycle_contribution($submitted_values)
    {
        $existing_metadata_id = $this->retrieve_existing_metadata_id(LearningObjectMetadata :: PROPERTY_PROPERTY, MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[', IeeeLom :: VERSION);
        
        $saved_metadata_id = array();
        
        //debug($existing_metadata);
        
        if(isset($submitted_values[MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION]))
        {
             //debug($submitted_values[MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION]);
             
             foreach ($submitted_values[MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION] as $key => $contribution) 
             {
                 //debug($contribution);
                 
                 try
                 {
                     /*
                      * Save Role
                      */
                     $meta_data = $this->get_new_learning_object_metadata($contribution['role_metadata_id'], IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role]', $contribution['role'], $contribution['contribution_override_id']);
                     $meta_data->save();
                     $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                 
                     /*
            	     * Set the new generated ID in the form 
            	     */
                     $input_name = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][role_metadata_id]';
                     $this->store_constant_value($input_name, $meta_data->get_id());
                     
                     try
                     {
                         /*
                          * Save Date
                          */
                         $ieee_lom_dt = new IeeeLomDatetime();
                         $ieee_lom_dt->set_day($contribution['date']['day']);
                         $ieee_lom_dt->set_month($contribution['date']['month']);
                         $ieee_lom_dt->set_year($contribution['date']['year']);
                         $ieee_lom_dt->set_hour($contribution['date']['hour']);
                         $ieee_lom_dt->set_min($contribution['date']['min']);
                         $ieee_lom_dt->set_sec($contribution['date']['sec']);
                         
                         $meta_data = $this->get_new_learning_object_metadata($contribution['date']['date_metadata_id'], IeeeLom :: VERSION, MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date]', $ieee_lom_dt->get_datetime(), $contribution['contribution_override_id']);
                         $meta_data->save();
                         $saved_metadata_id[$meta_data->get_id()] = $meta_data;
                     
                         /*
                	     * Set the new generated ID in the form 
                	     */
                         $input_name = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][date][date_metadata_id]';
                         $this->store_constant_value($input_name, $meta_data->get_id());
                         
                         try
                         {
                             /*
                              * Save Entity-ies
                              */
                             
                             foreach ($contribution['entity'] as $entity_index => $entity) 
                             {
                             	//debug($entity_index);
                             	//debug($entity);
                             	$input_name_name           = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][name]';                                     
                                $input_name_email          = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][email]';
                                $input_name_org            = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][organisation]';
                                $input_name_name_metadata  = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][name_metadata_id]';                                     
                                $input_name_email_metadata = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][email_metadata_id]';
                                $input_name_org_metadata   = MetadataLOMEditForm :: LOM_LIFECYCLE_CONTRIBUTION . '[' . $key . '][entity][' . $entity_index . '][organisation_metadata_id]';
                                
                                
                             	$meta_data_name  = $this->get_new_learning_object_metadata($entity['name_metadata_id'], IeeeLom :: VERSION, $input_name_name, $entity['name'], $entity['override_id']);
                             	$meta_data_email = $this->get_new_learning_object_metadata($entity['email_metadata_id'], IeeeLom :: VERSION, $input_name_email, $entity['email'], $entity['override_id']);
                             	$meta_data_org   = $this->get_new_learning_object_metadata($entity['organisation_metadata_id'], IeeeLom :: VERSION, $input_name_org, $entity['organisation'], $entity['override_id']);
                             	
                             	try
                             	{
                             	    if(strlen($meta_data_name->get_value()) > 0)
                             	    {
                                 	    $meta_data_name->save();
                                 	    $saved_metadata_id[$meta_data_name->get_id()] = $meta_data_name;
                             	    }
                             	    
                             	    if(strlen($meta_data_email->get_value()) > 0)
                             	    {
                                 	    $meta_data_email->save();
                                 	    $saved_metadata_id[$meta_data_email->get_id()] = $meta_data_email;
                             	    }
                             	    
                             	    if(strlen($meta_data_org->get_value()) > 0)
                             	    {
                                 	    $meta_data_org->save();
                                 	    $saved_metadata_id[$meta_data_org->get_id()] = $meta_data_org;
                             	    }
                             	    
                             	    /*
                            	     * Set the new generated ID in the form 
                            	     */
                                     $this->store_constant_value($input_name_name_metadata, $meta_data_name->get_id());
                                     $this->store_constant_value($input_name_email_metadata, $meta_data_email->get_id());
                                     $this->store_constant_value($input_name_org_metadata, $meta_data_org->get_id());
                             	}
                             	catch(Exception $ex)
                             	{
                             	    $this->add_error($ex->getMessage());
                             	}
                             }
                         }
                         catch(Exception $ex)
                         {
                              $this->add_error($ex->getMessage());
                         }
                     }
                     catch(Exception $ex)
                     {
                          $this->add_error($ex->getMessage());
                     }
                 }
                 catch(Exception $ex)
                 {
                      $this->add_error($ex->getMessage());
                 }
             }   

            /*
             * Delete metadata that were not sent back for saving
             */
            $this->delete_non_saved_metadata($existing_metadata_id, $saved_metadata_id);
        }
    }
    
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