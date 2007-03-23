<?php
/**
 * $Id$
 * @package repository.metadata
 * @subpackage ieee_lom
 */
require_once(dirname(__FILE__).'/ieee_lom.class.php');
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
		$lom = new IeeeLom();
		$lom->add_identifier(api_get_setting('InstitutionUrl'),$learning_object->get_id());
		$lom->add_title(new LangString($learning_object->get_title(),'x-none'));
		$lom->add_description(new LangString($learning_object->get_description(),'x-none'));
		$owner = api_get_user_info($learning_object->get_owner_id());
		$lom->set_version(new Langstring($learning_object->get_learning_object_edition(),'x-none'));
		$lom->set_status(new Vocabulary('LOMV1.0',($learning_object->is_latest_version() == TRUE ? 'final' : 'draft')));
		$all_versions = $learning_object->get_learning_object_versions();
		foreach($all_versions as $version)
		{
			$versionowner = api_get_user_info($version->get_owner_id());
			$vcard = new Contact_Vcard_Build();
			$vcard->addEmail($versionowner['mail']);
			$vcard->setFormattedName($versionowner['firstName'].' '.$versionowner['lastName']);
			$vcard->setName($versionowner['lastName'].' '.$versionowner['firstName']);
			$lom->add_contribute(new Vocabulary('LOMV1.0',$versionowner == $owner ? 'author' : 'editor'),$vcard->fetch(),new IeeeLomDateTime(date('Y-m-d\TH:i:sO',$version->get_creation_date())));
		}
		//$lom->add_contribute(new Vocabulary('LOMV1.0','author'),$vcard->fetch(),new IeeeLomDateTime(date('Y-m-d\TH:i:sO',$learning_object->get_creation_date())));
		//$lom->add_contribute(new Vocabulary('LOMV1.0','author'),$vcard->fetch(),new IeeeLomDateTime(date('Y-m-d\TH:i:sO',$learning_object->get_creation_date())));
		$vcard = new Contact_Vcard_Build();
		$vcard->setFormattedName(api_get_setting('Institution'));
		$vcard->setName(api_get_setting('siteName'));
		$vcard->addOrganization(api_get_setting('Institution'));
		$vcard->setURL(api_get_setting('InstitutionUrl'));
		$lom->add_metadata_identifier(api_get_setting('Institution'),$learning_object->get_id());
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