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
	function ScormImport($learning_object_file, $user, $category)
	{
		parent :: __construct($learning_object_file, $user, $category);	
	}
	
	public function import_learning_object()
	{
		$zip = Filecompression :: factory();
		$extracted_files_dir = $zip->extract_file($this->get_learning_object_file_property('tmp_name'));
	
		$manifest_file = $extracted_files_dir . '/imsmanifest.xml';
		$xml_data = $this->extract_xml_file($manifest_file);
		
		$dir = Path :: get(SYS_SCORM_PATH) . $this->get_user()->get_id() . '/' . $xml_data['identifier'] . '/';
		FileSystem :: move_file($extracted_files_dir, $dir);
		
		$manifest_file = $dir . '/imsmanifest.xml';
		$xml_data = $this->extract_xml_file($manifest_file);
		
		//$this->build_organizations($xml_data['organizations']['organization']);
		
		FileSystem :: remove($extracted_files_dir);
		
	}
	
	private function build_organizations($organizations)
	{
		foreach($organizations as $organization)
		{
			$learning_path = $this->create_learning_path($organization['title']);
			
			foreach($organization['item'] as $item)
			{
				$scorm_item = $this->create_scorm_item($item);
				$this->add_scorm_item_to_learning_path($scorm_item, $learning_path);
			}
		}
	}
	
	private function extract_xml_file($file)
	{
		$options = array(XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('item', 'organization', 'resource'));
		return DokeosUtilities :: extract_xml_file($file, $options);
	}
	
	// Learning Object Methods
	
	/**
	 * Creates a learning path from a title
	 *
	 * @param String $title
	 * @return LearningPath
	 */
	private function create_learning_path($title)
	{
		$learning_path = AbstractLearningObject :: factory('learning_path');
		$learning_path->set_title($title);
		$learning_path->set_description($title);
		$learning_path->set_parent_id($this->get_category());
		$learning_path->set_owner_id($this->get_user()->get_id());
		$learning_path->create();
		
		return $learning_path;
	}
	
	/**
	 * Creates a SCORM item from an item tag in the imsmanifest.xml
	 *
	 * @param Array[String] $item
	 * @return ScormItem
	 */
	private function create_scorm_item($item)
	{
		$scorm_item = AbstractLearningObject :: factory('scorm_item');
		$scorm_item->set_title($item['title']);
		$scorm_item->set_description($item['title']);
		$scorm_item->set_parent_id($this->get_category());
		$scorm_item->set_owner_id($this->get_user()->get_id());
		
		if($item['isvisible'])
			$scorm_item->set_visible(($item['isvisible'] == 'true'));
		
		if($item['parameters'])
			$scorm_item->set_parameters($item['parameters']);
	
		if($item['adlcp:completionTreshold'])
			$scorm_item->set_completion_treshold($item['adlcp:completionTreshold']);
		
		if($item['adlcp:dataFromLMS'])
			$scorm_item->set_data_from_lms($item['adlcp:dataFromLMS']);
		
		if($item['adlcp:timeLimitAction'])
			$scorm_item->set_time_limit_action($item['adlcp:timeLimitAction']);
		
		$scorm_item->create();
		
		return $scorm_item;
	}
	
	/**
	 * Adds a SCORM item to a learning path
	 *
	 * @param ScormItem $scorm_item
	 * @param LearningPath $learning_path
	 * @return ComplexScormItem - The wrapper
	 */
	private function add_scorm_item_to_learning_path($scorm_item, $learning_path)
	{
		$learning_path_item = AbstractLearningObject :: factory('learning_path_item');
		$learning_path_item->set_parent_id($this->get_category());
		$learning_path_item->set_owner_id($this->get_user()->get_id());
		$learning_path_item->set_title($scorm_item->get_title());
		$learning_path_item->set_description($scorm_item->get_description());
		$learning_path_item->set_reference($scorm_item->get_id());
		$learning_path_item->create();
		
		$wrapper = ComplexLearningObjectItem :: factory('learning_path_item');
		$wrapper->set_ref($learning_path_item->get_id());
		$wrapper->set_parent($learning_path->get_id());
		$wrapper->set_user_id($this->get_user()->get_id());
		$wrapper->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($learning_path->get_id()));
		$wrapper->create();
		
		return $wrapper;
	}
	
	/**
	 * Adds a sub learning path to a learning path
	 * @param LearningPath $sub_learning_path
	 * @param LearningPath $learning_path
	 * @return ComplexScormItem - The wrapper
	 */
	private function add_sub_learning_path_to_learning_path($learning_path, $sub_learning_path)
	{
		$wrapper = ComplexLearningObjectItem :: factory('learning_path_item');
		$wrapper->set_ref($sub_learning_path->get_id());
		$wrapper->set_parent($learning_path->get_id());
		$wrapper->set_user_id($this->get_user()->get_id());
		$wrapper->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($learning_path->get_id()));
		$wrapper->create();
		
		return $wrapper;
	}
}
?>