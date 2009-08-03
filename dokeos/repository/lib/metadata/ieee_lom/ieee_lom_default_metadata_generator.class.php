<?php
/**
 * $Id: ieee_lom_generator.class.php 16511 2008-10-13 16:54:08Z scara84 $
 * @package repository.metadata
 * @subpackage ieee_lom
 */
require_once(dirname(__FILE__).'/ieee_lom.class.php');
require_once Path :: get_user_path(). 'lib/user_data_manager.class.php';
require_once Path :: get_admin_path().'lib/admin_data_manager.class.php';
/**
 * This class automatically generates IEEE LOM compatible metadata for learning
 * objects.
 */
class IeeeLomDefaultMetadataGenerator
{
    private $ieeeLom;
    private $user_data_manager;
    private $admin_data_manager;
    private $learning_object;
    private $additional_metadata;
    
    public function IeeeLomDefaultMetadataGenerator()
    {
        $this->ieeeLom             = new IeeeLom();
        $this->user_data_manager   = UserDataManager :: get_instance();
		$this->admin_data_manager  = AdminDataManager :: get_instance();
    }
    
    
    function set_learning_object($learning_object)
    {
        $this->learning_object = $learning_object;
    }
    
    
    /************************************************************************************/
    
	/**
	 * This function will generate some default metadata from a given learning
	 * object. 
	 * 
	 * These default metadata could then be extended by using the IeeeLomMapper class
	 * 
	 * @param LearningObject $learning_object
	 * @return IeeeLom the generated metadata
	 */
	public function generate()
	{
	    $this->add_general();
		$this->add_lifeCycle();
	    $this->add_metametadata();
	    
		if(method_exists('IeeeLomDefaultMetadataGenerator','add_specific_metadata_for_' . $this->learning_object->get_type()))
		{
			call_user_func(array('IeeeLomDefaultMetadataGenerator','add_specific_metadata_for_' . $this->learning_object->get_type()), $this->learning_object, $this->ieeeLom);
		}
		
		return $this->ieeeLom;	
	}
	
	
	private function add_general()
	{
	    $this->ieeeLom->add_title(new LangString($this->learning_object->get_title(), IeeeLom :: NO_LANGUAGE));
	    $this->ieeeLom->add_identifier(PlatformSetting :: get('institution_url', 'admin'), $this->learning_object->get_id());
	    $this->ieeeLom->add_description(new LangString($this->learning_object->get_description(), IeeeLom :: NO_LANGUAGE));
	}
	
	private function add_lifeCycle()
	{
	    $owner = $this->user_data_manager->retrieve_user($this->learning_object->get_owner_id());

	    $this->ieeeLom->set_version(new Langstring($this->learning_object->get_learning_object_edition(), IeeeLom :: NO_LANGUAGE));
	    $this->ieeeLom->set_status(new IeeeLomVocabulary(($this->learning_object->is_latest_version() == TRUE ? 'final' : 'draft')));
	    
	    $all_versions = $this->learning_object->get_learning_object_versions();
		foreach($all_versions as $version)
		{
			$versionowner = $this->user_data_manager->retrieve_user($version->get_owner_id());
			
			$vcard = new Contact_Vcard_Build();
			$vcard->addEmail(        $versionowner->get_email());
			$vcard->setFormattedName($versionowner->get_firstname() . ' ' . $versionowner->get_lastname());
			$vcard->setName(         $versionowner->get_lastname() . ' ' . $versionowner->get_firstname());
			$this->ieeeLom->add_contribute(new IeeeLomVocabulary($versionowner == $owner ? 'author' : 'editor'), $vcard->fetch(), new IeeeLomDateTime($version->get_creation_date()));
		}
	}
	
	private function add_metametadata()
	{
	    $this->ieeeLom->add_metadata_schema(IeeeLom :: VERSION);
	    $this->ieeeLom->add_metadata_identifier(PlatformSetting :: get('institution', 'admin'), $this->learning_object->get_id());
	    
	    $vcard = new Contact_Vcard_Build();
		$vcard->setFormattedName(PlatformSetting :: get('institution',     'admin'));
		$vcard->setName(         PlatformSetting :: get('site_name',       'admin'));
		$vcard->addOrganization( PlatformSetting :: get('institution',     'admin'));
		$vcard->setURL(          PlatformSetting :: get('institution_url', 'admin'));
		$this->ieeeLom->add_metadata_contribute(new IeeeLomVocabulary('creator'), $vcard->fetch(), new IeeeLomDateTime(date('Y-m-d\TH:i:sO')));
	}
	
	
	/************************************************************************************/
	
	/**
	 * This function will add some document specific metadata.
	 * @param LearningObject $learning_object
	 * @param IeeeLom $lom The metadata to extend
	 */
	function add_specific_metadata_for_document($learning_object, $ieeeLom)
	{
		$this->ieeeLom->set_size($this->learning_object->get_filesize());
		
		//TODO: FileInfo is an experimental extension of PHP.
		//$finfo = finfo_open(FILEINFO_MIME,'C:/wamp/php/extras/magic');
		//$lom->add_format(finfo_file($finfo, $learning_object->get_full_path()));
		//finfo_close($finfo);
	}	
	
	function add_specific_metadata_for_wiki($learning_object, $ieeeLom)
	{
	    
	}
	
	function add_specific_metadata_for_link($learning_object, $ieeeLom)
	{
	    
	}
	
	/*
	 * 
	 *
	function add_specific_metadata_for_... ($learning_object, $ieeeLom)
	{
	    
	}
	*/
}
?>