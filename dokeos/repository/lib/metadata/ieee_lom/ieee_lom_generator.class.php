<?php
/**
 * $Id$
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
class IeeeLomGenerator
{
	/**
	 * This function will generate some default metadata from a given learning
	 * object.
	 * @param LearningObject $learning_object
	 * @return IeeeLom the generated metadata
	 */
	static function generate($learning_object)
	{
		$udm = UserDataManager :: get_instance();
		$adm = AdminDataManager :: get_instance();
		
		$lom = new IeeeLom();
		$lom->add_identifier(PlatformSetting :: get('institution_url', 'admin'),$learning_object->get_id());
		$lom->add_title(new LangString($learning_object->get_title(),'x-none'));
		$lom->add_description(new LangString($learning_object->get_description(),'x-none'));
		$owner = $udm->retrieve_user($learning_object->get_owner_id());
		$lom->set_version(new Langstring($learning_object->get_learning_object_edition(),'x-none'));
		$lom->set_status(new Vocabulary('LOMV1.0',($learning_object->is_latest_version() == TRUE ? 'final' : 'draft')));
		$all_versions = $learning_object->get_learning_object_versions();
		foreach($all_versions as $version)
		{
			$versionowner = $udm->retrieve_user($version->get_owner_id());
			$vcard = new Contact_Vcard_Build();
			$vcard->addEmail($versionowner->get_email());
			$vcard->setFormattedName($versionowner->get_firstname().' '.$versionowner->get_lastname());
			$vcard->setName($versionowner->get_lastname().' '.$versionowner->get_firstname());
			$lom->add_contribute(new Vocabulary('LOMV1.0',$versionowner == $owner ? 'author' : 'editor'),$vcard->fetch(),new IeeeLomDateTime(date('Y-m-d\TH:i:sO',$version->get_creation_date())));
		}
		$vcard = new Contact_Vcard_Build();
		$vcard->setFormattedName(PlatformSetting :: get('institution', 'admin'));
		$vcard->setName(PlatformSetting :: get('site_name', 'admin'));
		$vcard->addOrganization(PlatformSetting :: get('institution', 'admin'));
		$vcard->setURL(PlatformSetting :: get('institution_url', 'admin'));
		$lom->add_metadata_identifier(PlatformSetting :: get('institution', 'admin'),$learning_object->get_id());
		$lom->add_metadata_contribute(new Vocabulary('LOMv1.0','creator'),$vcard->fetch(),new IeeeLomDateTime(date('Y-m-d\TH:i:sO')));
		$lom->add_metadata_schema('LOMv1.0');
		if(method_exists('IeeeLomGenerator','generate_'.$learning_object->get_type()))
		{
			call_user_func(array('IeeeLomGenerator','generate_'.$learning_object->get_type()),$learning_object,$lom);
		}
		return $lom;
	}
	/**
	 * This function will add some document specific metadata.
	 * @param LearningObject $learning_object
	 * @param IeeeLom $lom The metadata to extend
	 */
	static function generate_document($learning_object,$lom)
	{
		$lom->set_size($learning_object->get_filesize());
		//TODO: FileInfo is an experimental extension of PHP.
		//$finfo = finfo_open(FILEINFO_MIME,'C:/wamp/php/extras/magic');
		//$lom->add_format(finfo_file($finfo, $learning_object->get_full_path()));
		//finfo_close($finfo);
	}
}
?>