<?php
require_once dirname(__FILE__) . '/../../../common/html/formvalidator/FormValidator.class.php';
require_once dirname(__FILE__) . '/../repository_manager/repository_manager.class.php';
require_once dirname(__FILE__) . '/../repository_data_manager.class.php';

class MetadataLOMEditForm extends FormValidator
{
    const FORM_ACTION              = 'lom_form_action';
    const FORM_ACTION_VALUE        = 'lom_form_action_value';
    
    const FORM_ACTION_ADD_GENERAL_TITLE            = 'add_title';
    const FORM_ACTION_REMOVE_GENERAL_TITLE         = 'remove_title';
    const FORM_ACTION_ADD_GENERAL_IDENTIFIER       = 'add_identifier';
    const FORM_ACTION_REMOVE_GENERAL_IDENTIFIER    = 'remove_idenfitier';
    
    const FORM_ACTION_SAVE    = 'save';
    
    const FORM_WIDTH_LARGE    = 300;
    const FORM_WIDTH_MEDIUM   = 200;
    const FORM_WIDTH_SMALL    = 100;
    
    const LOM_GENERAL_IDENTIFIER                = 'general_identifier';
    const LOM_GENERAL_TITLE                     = 'general_title';
    const LOM_LIFECYCLE_CONTRIBUTION            = 'lifeCycle_contribution';
    
    const MSG_FORM_HAS_UPDATE = 'msg_form_has_update';
    
	private $ieee_lom_mapper;

	private $catalogs;
	private $current_values;
	private $constants;
	private $skipped_indexes;
	private $info_messages;
	
	public function MetadataLOMEditForm($learning_object_id, $ieee_lom_mapper, $action, $catalogs)
	{
		parent :: __construct('lom_metadata', 'post', $action);
		
		/*
		 * Init 
		 */
		$this->current_values     = array();
		$this->constants          = array();
		$this->learning_object_id = $learning_object_id;
		$this->catalogs           = $catalogs;
		
		/*
		 * Set the lom object for the Form.
		 * If the form was submitted, it retrieves the lom from the session
		 */
		$this->init_lom_mapper($this->learning_object_id, $ieee_lom_mapper);
		$this->init_info_messages();
		
		//$this->build_editing_form();
	}
	
	
	/*************************************************************************/
	
	/*
	 * Build the form on page
	 * QuickForm will allow to automatically repopulate 
	 * submitted fields (if the form was submitted of course) 
	 */
	function build_editing_form()
	{
	    $this->init_skipped_indexes();
		
		/*
		 * Do any action asked by a click on the form
		 * (e.g. add a field)
		 */
		$this->manage_form_action();
		//debug($this->skipped_indexes, 'skipped indexes');
		
		/*
		 * At this step, all the Lom document modifications 
		 * (if any requested) have been done
		 * -> save the IeeeLomMapper in session to reuse after the next form postback 
		 */
		$this->store_lom_mapper($this->learning_object_id, $this->ieee_lom_mapper);
		
		/*
		 * At this step, the eventual new skipped indexes are set 
		 */
		$this->store_skipped_indexes($this->skipped_indexes);
	    
	    $this->build_general_identifier($this->ieee_lom_mapper);
		$this->build_general_title($this->ieee_lom_mapper);
		//$this->build_lifeCycle_contribution($this->ieee_lom_mapper);
		
		$this->set_action_fields();
		$this->add_submit_buttons();
		
		/*
		 * Set original default form values
		 */
		parent :: setDefaults($this->current_values);
		
		/*
		 * Set original form values that must not change between postbacks
		 */
		parent :: setConstants($this->constants);
	}

	/*** General **********************************************************************/
	
	/**
	 * 1.1 Identifier
	 * 
	 * @param $ieee_lom_mapper
	 * @return void
	 */
	private function build_general_identifier($ieee_lom_mapper)
	{	    
	    $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMIdentifiers') . '</h3>');
	     
	    $data = $ieee_lom_mapper->get_identifier();
	     
        //debug($data);
	     
	    for($index = 0; $index < count($data); $index++)
	     {
	        /*
	         * General.Identifier (1 -> n)
	         */
	        $show_remove_button = ($index == 0) ? false : true;
	        
	        if(!isset($this->skipped_indexes[self :: LOM_GENERAL_IDENTIFIER][$index]))
	        {
	            $catalog_metadata_id   = isset($data[$index][IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE]) && strlen($data[$index][IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE]) > 0   ? $data[$index][IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE]   : RepositoryModelObject :: NO_UID;
                $entry_metadata_id     = isset($data[$index][IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE]) && strlen($data[$index][IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE]) > 0 ? $data[$index][IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE] : RepositoryModelObject :: NO_UID;
	            $original_id           = isset($data[$index][IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE]) && strlen($data[$index][IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE]) > 0 ? $data[$index][IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE] : RepositoryModelObject :: NO_UID;
	            
	            $group_fields   = array();
	            $group_fields[] = $this->create_textfield('catalog', Translation :: translate('MetadataLomCatalog'), array('style' => 'width:' . self :: FORM_WIDTH_LARGE . 'px'));
	            $group_fields[] = $this->create_textfield('entry', Translation :: translate('MetadataLomEntry'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
	            
	            $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE);
        	    $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE);
        	    $group_fields[] = $this->createElement('hidden', IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE);
	    
    	        if($show_remove_button)
        	    {
        	        $group_fields[] = $this->createElement('image', 'remove_identifier_' . $index , Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE_GENERAL_IDENTIFIER . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
        	    }
	    
        	    $renderer = $this->defaultRenderer();
        	    $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', self :: LOM_GENERAL_IDENTIFIER . '[' . $index . ']');
        	    
        	    $this->addGroup($group_fields, self :: LOM_GENERAL_IDENTIFIER . '[' . $index . ']', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
        	    
        	    $rule = array();
        	    $rule['catalog'][] = array(Translation :: translate('MetadataLomCatalogEntryEmptyError'), 'required');
        	    $rule['entry'][]   = array(Translation :: translate('MetadataLomCatalogEntryEmptyError'), 'required');
        	    $this->addGroupRule(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . ']', $rule);	  
        	    
        	    $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][catalog]',               $data[$index]['catalog']);
    	        $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][entry]',             $data[$index]['entry']);
    	        $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: METADATA_ID_CATALOG_ATTRIBUTE . ']',   $catalog_metadata_id);
    	        $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: METADATA_ID_ENTRY_ATTRIBUTE . ']', $entry_metadata_id);
    	        $this->set_current_value(self :: LOM_GENERAL_IDENTIFIER . '[' . $index . '][' . IeeeLomMapper :: ORIGINAL_ID_ATTRIBUTE . ']',   $original_id);
	        }
	     }
	    
	    /*
	     * Add the "add title" button
	     */
	     $this->addElement('image', self :: FORM_ACTION_ADD_GENERAL_IDENTIFIER, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_GENERAL_IDENTIFIER . "')"));
	}
	
	/**
	 * 1.2 Title
	 * 
	 * @param $ieee_lom_mapper
	 * @return void
	 */
	private function build_general_title($ieee_lom_mapper)
	{	    
	    $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMTitles') . '</h3>');
	    
	    $titles = $ieee_lom_mapper->get_titles();
	    $strings = $titles->get_strings();
	    
	    //debug($strings);
	    
	    for($index = 0; $index < count($strings); $index++)
	    {
	        /*
	         * General.Title (1 -> n)
	         */
	        $show_remove_button = ($index == 0) ? false : true;
	        
	        if(!isset($this->skipped_indexes[self :: LOM_GENERAL_TITLE][$index]))
	        {
                $string_metadata_id   = isset($strings[$index][LangStringMapper :: STRING_METADATA_ID]) && strlen($strings[$index][LangStringMapper :: STRING_METADATA_ID]) > 0   ? $strings[$index][LangStringMapper :: STRING_METADATA_ID]   : RepositoryModelObject :: NO_UID;
                $language_metadata_id = isset($strings[$index][LangStringMapper :: LANGUAGE_METADATA_ID]) && strlen($strings[$index][LangStringMapper :: LANGUAGE_METADATA_ID]) > 0 ? $strings[$index][LangStringMapper :: LANGUAGE_METADATA_ID] : RepositoryModelObject :: NO_UID;
	            $string_original_id   = isset($strings[$index][LangStringMapper :: STRING_ORIGINAL_ID]) && strlen($strings[$index][LangStringMapper :: STRING_ORIGINAL_ID]) > 0 ? $strings[$index][LangStringMapper :: STRING_ORIGINAL_ID] : RepositoryModelObject :: NO_UID;
                
	            $this->add_lang_string(self :: LOM_GENERAL_TITLE, $index, Translation :: translate('MetadataLOMTitle'), true, $show_remove_button, $string_metadata_id, $language_metadata_id, $string_original_id);
	        
//	            if($index == 0)
//    	        {
//    	            $rule = array();
//    	            $rule['string'][] = array('The title can not be empty', 'required');
//    	            
//    	            $this->addGroupRule(self :: LOM_GENERAL_TITLE . '[' . $index . ']', $rule);    
//    	        }
    	        
    	        $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][string]',               $strings[$index]['string']);
    	        $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][language]',             $strings[$index]['language']);
    	        $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][string_metadata_id]',   $string_metadata_id);
    	        $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][language_metadata_id]', $language_metadata_id);
    	        $this->set_current_value(self :: LOM_GENERAL_TITLE . '[' . $index . '][string_original_id]',   $string_original_id);
	        }
	    }
	    
	    /*
	     * Add the "add title" button
	     */
	    $this->addElement('image', self :: FORM_ACTION_ADD_GENERAL_TITLE, Theme :: get_common_image_path() . 'action_add.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_ADD_GENERAL_TITLE . "')"));
	}
	
	
	/*** Life Cycle *******************************************************************/
	
	/**
	 * 2.3 Contribution
	 * 
	 * @param $ieee_lom_mapper
	 * @return void
	 */
	private function build_lifeCycle_contribution($ieee_lom_mapper)
	{
	    $this->addElement('html', '<h3>' . Translation :: translate('MetadataLOMContribution') . '</h3>');
	    
	    $data = $ieee_lom_mapper->get_contribution();
	    //debug($contributions);
	    
	    /*
	     * Create contributions
	     */
	    for($index = 0; $index < count($data); $index++)
	    {
	    	//debug($data[$index]['entity'][0]->getValue('FN'));
	    	//debug($data[$index]['role'], 'ROLE');
	    	
	    	$group_name = self :: LOM_LIFECYCLE_CONTRIBUTION;
	    	
	    	/*
	    	 * Create role for contribution
	    	 */
	    	$this->add_role($group_name, $index, $data[$index]['role']);
	    	
	    	/*
	    	 * Create entities for contribution
	    	 */
	    	$tot_entity = count($data[$index]['entity']);
	    	$tot_entity = ($tot_entity > 0)? $tot_entity : 1;
	    	
	    	for($entity_index = 0; $entity_index < $tot_entity; $entity_index++)
	    	{
	    	    $group_name_entity = $group_name . '[' . $index . '][entity]';
    	    	$vcard = $data[$index]['entity'][$entity_index];
    	    	$this->add_entity($group_name_entity, $entity_index, $vcard);
	    	}
	    	
	    	/*
	    	 * Add date for contribution
	    	 */
	    	$this->add_datetime($group_name, $index, $data[$index]['role']);
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Add hidden fields allowing to send back to the server the action 
	 * that has to be done on the form 
	 * (e.g. add / remove a field)
	 */
	private function set_action_fields()
	{
	    $this->addElement('hidden', self :: FORM_ACTION, null, array('id' => self :: FORM_ACTION));
	    $this->addElement('hidden', self :: FORM_ACTION_VALUE, null, array('id' => self :: FORM_ACTION_VALUE));
	    
		$this->constants[self :: FORM_ACTION]       = '';
		$this->constants[self :: FORM_ACTION_VALUE] = '';
	}
	
	/**
	 * Add the submit button to the form
	 */
	private function add_submit_buttons()
	{
	    $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update', 'onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_SAVE . "')"));
		//$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
		$this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
	}
	
	
	/*************************************************************************/
	
	/**
	 * Treats an action made on the form
	 */
	private function manage_form_action()
	{
	    $action = Request :: post(self :: FORM_ACTION);
	    
	    //debug($action);
	    
	    switch($action)
	    {
	        case self :: FORM_ACTION_ADD_GENERAL_IDENTIFIER:
	            $this->add_identifier();
	            break;
	            
	        case self :: FORM_ACTION_REMOVE_GENERAL_IDENTIFIER:
	            $this->remove_identifier();
	            break;
	            
	        case self :: FORM_ACTION_ADD_GENERAL_TITLE:
	            $this->add_title();
	            break;
	            
	        case self :: FORM_ACTION_REMOVE_GENERAL_TITLE:
	            $this->remove_title();
	            break;
	    }
	}
	
	/**
	 * Add a blank identifier
	 */
	private function add_identifier()
	{
	    $this->ieee_lom_mapper->add_general_identifier('', '');
	    
	    $this->add_info_message(self :: MSG_FORM_HAS_UPDATE);
	}
	
	/**
	 * Remove a clicked identifier
	 */
	private function remove_identifier()
	{
	    $action_value = Request :: post(self :: FORM_ACTION_VALUE);
	    
	    if(isset($action_value) && is_numeric($action_value))
	    {
	        $this->skipped_indexes[self :: LOM_GENERAL_IDENTIFIER][$action_value] = true;
	        
	        $this->add_info_message(self :: MSG_FORM_HAS_UPDATE);
	    }
	}
	
	/**
	 * Add a blank title
	 */
	private function add_title()
	{
	    $this->ieee_lom_mapper->add_general_title(new LangString('', ''), -1);
	    
	    $this->add_info_message(self :: MSG_FORM_HAS_UPDATE);
	}
	
	/**
	 * Remove a clicked title
	 */
	private function remove_title()
	{
	    $action_value = Request :: post(self :: FORM_ACTION_VALUE);
	    
	    if(isset($action_value) && is_numeric($action_value))
	    {
	        $this->skipped_indexes[self :: LOM_GENERAL_TITLE][$action_value] = true;
	        
	        $this->add_info_message(self :: MSG_FORM_HAS_UPDATE);
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Add a textfield and a combobox for the language
	 * 
	 * @param string $group_name
	 * @param integer $index
	 * @param string $label
	 * @param bool $with_lang_empty_value
	 * @param bool $show_remove_button
	 * @param integer $string_metadata_id
	 * @param integer $language_metadata_id
	 * @param integer $string_original_id
	 * @return void
	 */
	private function add_lang_string($group_name, $index, $label, $with_lang_empty_value = true, $show_remove_button = false, $string_metadata_id = RepositoryModelObject :: NO_UID, $language_metadata_id = RepositoryModelObject :: NO_UID, $string_original_id = RepositoryModelObject :: NO_UID)
	{
	    $group_fields   = array();

	    $group_fields[] = $this->create_textfield('string', $label, array('style' => 'width:' . self :: FORM_WIDTH_LARGE . 'px'));
	    //$group_fields[] = $this->createElement('select', 'language', null, $this->get_lang_catalog($with_lang_empty_value));
	    $group_fields[] = $this->createElement('select', 'language', null, $this->get_catalog(LearningObjectMetadataCatalog :: CATALOG_LANGUAGE));
	    $group_fields[] = $this->createElement('hidden', 'string_metadata_id');
	    $group_fields[] = $this->createElement('hidden', 'language_metadata_id');
	    $group_fields[] = $this->createElement('hidden', 'string_original_id');
	    
	    if($show_remove_button)
	    {
	        $group_fields[] = $this->createElement('image', 'remove_title_' . $index , Theme :: get_common_image_path() . 'action_delete.png', array('onclick' => "$('#" . self :: FORM_ACTION . "').val('" . self :: FORM_ACTION_REMOVE_GENERAL_TITLE . "');$('#" . self :: FORM_ACTION_VALUE . "').val('" . $index . "')"));
	    }
	    
	    $renderer = $this->defaultRenderer();
	    $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $index . ']');
	    
	    $this->addGroup($group_fields, $group_name . '[' . $index . ']');
	    
	    $rule = array();
	    $rule['string'][] = array(Translation :: translate('MetadataLOMTextEmptyError'), 'required');
	    $this->addGroupRule($group_name . '[' . $index . ']', $rule);	    
	}
	
	private function add_entity($group_name, $index, $vcard)
	{
	    $group_fields   = array();
	    $group_fields[] = $this->createElement('html', '<div class="row"><div class="formw"><h5>Entity</h5></div></div>');
        $group_fields[] = $this->create_textfield('name', Translation :: translate('MetadataLomContriName'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
        $group_fields[] = $this->create_textfield('email', Translation :: translate('MetadataLomContriEmail'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
        $group_fields[] = $this->create_textfield('organisation', Translation :: translate('MetadataLomContriOrg'), array('style' => 'width:' . self :: FORM_WIDTH_MEDIUM . 'px'));
    	
        $renderer = $this->defaultRenderer();
        $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $index . ']');

        $this->addGroup($group_fields, $group_name . '[' . $index . ']', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
    	    
        $rule = array();
        $rule['name'][] = array(Translation :: translate('MetadataLomCatalogEntryEmptyError'), 'required');
        $this->addGroupRule($group_name . '[' . $index . ']', $rule);
        
        if(isset($vcard))
        {
            $this->set_current_value($group_name . '[' . $index . '][name]',         $vcard->getValue('FN'));
    	    $this->set_current_value($group_name . '[' . $index . '][email]',        $vcard->getValue('EMAIL'));
    	    $this->set_current_value($group_name . '[' . $index . '][organisation]', $vcard->getValue('ORGANISATION'));
        }
	}
	
	private function add_role($group_name, $index, $role_value)
	{
	    //debug($group_name);
	    
	    $group_fields   = array();
	    $group_fields[] = $this->createElement('html', '<div class="row"><div class="formw"><h5>Role</h5></div></div>');
	    $group_fields[] = $this->createElement('select', 'role', Translation :: translate('MetadataLomRole'), $this->get_catalog(LearningObjectMetadataCatalog :: CATALOG_ROLE));
	    
	    $renderer = $this->defaultRenderer();
	    $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $index . ']');
	    
	    $this->addGroup($group_fields, $group_name . '[' . $index . ']', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
	    
	    $rule = array();
        $rule['role'][] = array(Translation :: translate('MetadataLomRoleEmptyError'), 'required');
        $this->addGroupRule($group_name . '[' . $index . ']', $rule);
	    
	    $this->set_current_value($group_name . '[' . $index . '][role]', $role_value);
	}
	
	private function add_datetime($group_name, $index, $date_value)
	{
	    //debug($group_name);
	    
	    $days   = array('' => '');
	    $months = array('' => '');
	    $years  = array('' => '');
	    $hours  = array('' => '');
	    $mins   = array('' => '');
	    $secs   = array('' => '');
	    
	    for($i = 1; $i <= 31; $i++) { $days[$i] = $i; }
    	for($i = 1; $i <= 12; $i++) { $months[$i] = $i; }
    	for($i = date('Y') + 2; $i >= 1900; $i--) { $years[$i] = $i; }
    	for($i = 0; $i < 24; $i++) { $hours[$i] = $i; }
    	for($i = 0; $i < 60; $i++) { $mins[$i] = $i; }
    	for($i = 0; $i < 60; $i++) { $secs[$i] = $i; }
    	
	    $group_fields   = array();
	    $group_fields[] = $this->createElement('html', '<div class="row"><div class="formw"><h5>Date</h5></div></div>');
	    $group_fields[] = $this->createElement('select', 'day', Translation :: translate('MetadataDay'), $days);
	    $group_fields[] = $this->createElement('select', 'month', Translation :: translate('MetadataMonth'), $months);
	    $group_fields[] = $this->createElement('select', 'year', Translation :: translate('MetadataYear'), $years);
	    $group_fields[] = $this->createElement('select', 'hour', Translation :: translate('MetadataHour'), $hours);
	    $group_fields[] = $this->createElement('select', 'min', Translation :: translate('MetadataMin'), $mins);
	    $group_fields[] = $this->createElement('select', 'sec', Translation :: translate('MetadataSec'), $secs);
	    
	    $renderer = $this->defaultRenderer();
	    //$renderer->setGroupTemplate('&nbsp;&nbsp;&nbsp;&nbsp;Date {content}', $group_name . '[' . $index . '][date]');
	    $renderer->setGroupElementTemplate('<!-- BEGIN required -->' . Theme :: get_common_image('action_required') . '<!-- END required -->{label} {element}', $group_name . '[' . $index . '][date]');
	    
	    $this->addGroup($group_fields, $group_name . '[' . $index . '][date]', null, '&nbsp;&nbsp;&nbsp;&nbsp;');
	    
	}
	
	
	/**
	 * Get a list of values to fill a combobox
	 * 
	 * @param string $catalog_name
	 * @param bool $with_empty_value
	 * @return array
	 */
	private function get_catalog($catalog_name, $with_empty_value = true)
	{
	    if($with_empty_value)
	    {
	        return array_merge(array(''=>''), $this->catalogs[$catalog_name]);
	    }
	    else
	    {
	        return $this->catalogs[$catalog_name];
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Set the current value of a field
	 * 
	 * @param string $field_name
	 * @param string $value
	 * @param bool $override_existing Indicates wether an already existing value must be overriden
	 * @return void
	 */
	public function set_current_value($field_name, $value, $override_existing = false)
	{
	    if(!isset($this->current_values[$field_name]) || $override_existing)
	    {
	        $this->current_values[$field_name] = $value;
	    }
	}
	
	/**
	 * Set the constant value of a field.
	 * Useful for instance to set the value of generated Id of new metadata records 
	 * 
	 * @param string $field_name
	 * @param string $value
	 * @param bool $override_existing Indicates wether an already existing value must be overriden
	 * @return void
	 */
	public function set_constant_value($field_name, $value, $override_existing = false)
	{
	    if(!isset($this->constant[$field_name]) || $override_existing)
	    {
	        $this->constants[$field_name] = $value;
	    }
	}
	
	/**
	 * Set the constant values of many fields. 
	 * The $constant_values array must contains arrays with two keys 'name' and 'value':
	 * 
	 * [0] => 	
	 * 			[name] 	=> 	name
	 * 			[value]	=>	value
	 * [1] =>
	 * 			...
	 * 
	 * @param array $constant_values
	 * @param bool $override_existing
	 * @return void
	 */
	public function set_constant_values($constant_values, $override_existing = false)
	{
	    foreach ($constant_values as $constant) 
	    {
	    	$this->set_constant_value($constant['name'], $constant['value'], $override_existing);
	    }
	}
	
	
	/*************************************************************************/
	
	/**
	 * Init the IeeeLomMapper for the form. 
	 * If the form was posted, it tries to get it from the session.
	 * If the request is not a postback, it returns the given IeeeLomMapper
	 * 
	 * @param integer $learning_object_id
	 * @param IeeeLomMapper $ieee_lom_mapper
	 * @return void
	 */
	private function init_lom_mapper($learning_object_id, $ieee_lom_mapper)
	{
	    if($this->isSubmitted())
		{
		    $this->ieee_lom_mapper = $this->get_lom_mapper_from_session($learning_object_id);
		}
		else
		{
		    $ieeeLom = $ieee_lom_mapper->get_ieee_lom();
		    if(!isset($ieeeLom))
		    {
		        $ieee_lom_mapper->get_metadata();
		    }
		    
		    $this->ieee_lom_mapper = $ieee_lom_mapper;
		}
	}
	
	private function store_lom_mapper($learning_object_id, $ieee_lom_mapper)
	{
	    $_SESSION['ieee_lom_mapper_' . $learning_object_id] = $ieee_lom_mapper->get_ieee_lom()->get_dom()->saveXML();
	    $this->ieee_lom_mapper = $ieee_lom_mapper;
	}
	
	private function get_lom_mapper_from_session($learning_object_id)
	{
	    $dom_lom = new DOMDocument();
	    $dom_lom->loadXML($_SESSION['ieee_lom_mapper_' . $learning_object_id]);
	    
	    $ieee_lom_mapper = new IeeeLomMapper($learning_object_id);
	    $ieee_lom_mapper->set_ieee_lom(new IeeeLom($dom_lom));
	    $this->ieee_lom_mapper = $ieee_lom_mapper;

	    return $this->ieee_lom_mapper;
	}
	
	
	/**
	 * Init the info_messages array for the form
	 * If the form was posted, it tries to get it from the session.
	 * 
	 * @return void
	 */
	private function init_info_messages($learning_object_id)
	{
	    if($this->isSubmitted())
		{
		    $this->info_messages = $_SESSION['ieeeLom_info_messages_' . $learning_object_id];
		}
		else
		{
		    $_SESSION['ieeeLom_info_messages_' . $learning_object_id] = array();
		    $this->info_messages = $_SESSION['ieeeLom_info_messages_' . $learning_object_id]; 
		}
	}
	
	/**
	 * Add a message that must be displayed when the form is posted back 
	 * 
	 * @param string $key The key of the message. Allow to store the same message only once.
	 * @param string $message
	 * @return void
	 */
	private function add_info_message($key, $message)
	{
	    if(!array_key_exists($key, $this->info_messages))
	    {
	        switch($key)
	        {
	            case self :: MSG_FORM_HAS_UPDATE:
	                $this->info_messages[$key] = Translation :: translate('MetadataApplyWillBeDoneAfterClickUpdate');
	                break;
	            
	            default:
	                $this->info_messages[$key] = $message;
	                break;
	        }
	        
	        $_SESSION['ieeeLom_info_messages_' . $this->learning_object_id] = $this->info_messages;
	    }
	}
	
	/**
	 * 
	 * @return array The array of messages
	 */
	public function get_info_messages()
	{
	    return $this->info_messages;
	}
	
	/**
	 * Init the array containing the skipped indexes. 
	 * If the form was posted, it tries to get it from the session.
	 * 
	 * Note: the skipped_indexes array allow to hide some fields after a delete button has been clicked, 
	 * before all the updates are committed to the datasource.
	 * 
	 * @param string $key The key of the message. Allow to store the same message only once.
	 * @param string $message
	 * @return void
	 */
	private function init_skipped_indexes()
	{
	    if($this->isSubmitted())
		{
		    $skipped_indexes = $this->get_skipped_indexes_from_session();
		    $this->skipped_indexes = (isset($skipped_indexes)) ? $skipped_indexes : array() ;
		}
		else
		{
		    $this->skipped_indexes = array();
		}
	}
	
	private function store_skipped_indexes($skipped_indexes)
	{
	    $_SESSION['skipped_indexes_' . $this->learning_object_id] = $skipped_indexes;
	}
	
	private function get_skipped_indexes_from_session()
	{
	    return $_SESSION['skipped_indexes_' . $this->learning_object_id];
	}
	
	
	/*************************************************************************/
	
	/**
	 * Display the info messages and the form itself
	 * 
	 * @see dokeos/common/html/formvalidator/FormValidator#display()
	 */
	public function display()
	{
	    if(count($this->info_messages) > 0)
	    {
	        $messages = '<ul>';
	        
	        foreach ($this->info_messages as $message) 
	        {
	        	$messages .= '<li>' . $message . '</li>';
	        }
	        
	        $messages .= '</ul>';
	        
	        Display :: normal_message($messages);
	    }
	    
	    parent :: display();
	}
	
	
	/*
	 * Indicates wether the form is submitted and is valid
	 */
	public function must_save()
	{
	    $action = Request :: post(self :: FORM_ACTION);
	    
	    if($action == self :: FORM_ACTION_SAVE && $this->validate())
	    {
	        $this->info_messages = array();
	        
	        return true;
	    }
	    else
	    {
	        return false;
	    }
	}
	
}
?>