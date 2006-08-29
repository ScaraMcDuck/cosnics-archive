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
	static function generate($learning_object)
	{
		$lom = new IeeeLom();
		$lom->add_identifier(api_get_setting('InstitutionUrl'),$learning_object->get_id());
		$lom->add_title(new LangString($learning_object->get_title()));
		$lom->add_description(new LangString($learning_object->get_description()));
		$vcard = new Contact_Vcard_Build();
		$vcard->setFormattedName(api_get_setting('Institution'));
		$vcard->setName(api_get_setting('siteName'));
		$vcard->addOrganization(api_get_setting('Institution'));
		$vcard->setURL(api_get_setting('InstitutionUrl'));
		$lom->add_metadata_identifier(api_get_setting('Institution'),$learning_object->get_id());
		$lom->add_metadata_contribute(new Vocabulary('LOMv1.0','creator'),$vcard->fetch(),new DateTime(date('Y-m-d\TH:i:sO')));
		$lom->add_metadata_schema('LOMv1.0');
		if(method_exists('IeeeLomGenerator','generate_'.$learning_object->get_type()))
		{
			call_user_func(array('IeeeLomGenerator','generate_'.$learning_object->get_type()),$learning_object,$lom);
		}
		return $lom;
	}
	static function generate_document($learning_object,$lom)
	{
		$lom->set_size($learning_object->get_filesize());
		$finfo = finfo_open(FILEINFO_MIME,'C:/wamp/php/extras/magic');
		$lom->add_format(finfo_file($finfo, $learning_object->get_full_path()));
		finfo_close($finfo);
	}
}
?>