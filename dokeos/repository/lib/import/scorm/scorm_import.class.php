<?php
/**
 * @package export
 */
require_once dirname(__FILE__).'/../learning_object_import.class.php';
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';

/**
 * Exports learning object to the dokeos learning object format (xml)
 */
class ScormImport extends LearningObjectImport
{
	private $rdm;
	
	function ScormImport($learning_object_file, $user, $category)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		parent :: __construct($learning_object_file, $user, $category);	
	}
	
	public function import_learning_object()
	{
		$zip = Filecompression :: factory();
		$extracted_files_dir = $zip->extract_file($this->get_learning_object_file_property('tmp_name'));
		
		$manifest_file = $extracted_files_dir . '/imsmanifest.xml';
		$xml_data = $this->extract_xml_file($manifest_file);
		
		dump($xml_data);
		
		$organizations = $xml_data['organizations']['organization'];
		foreach($organizations as $organization)
		{
			dump($organization);
		}
		
		FileSystem :: remove($extracted_files_dir);
		
	}
	
	public function extract_xml_file($file)
	{
		$options = array(XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('item', 'organization', 'resource'));
		return DokeosUtilities :: extract_xml_file($file, $options);
	}
}
?>