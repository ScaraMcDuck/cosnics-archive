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
	
	/**
	 * Extract the xml file to an array
	 *
	 * @param String $file - Path to the file
	 * @return Array of Strings - The xml output
	 */
	private function extract_xml_file($file)
	{
		$options = array(XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('item', 'organization', 'resource', 'file', 'dependency'));
		return DokeosUtilities :: extract_xml_file($file, $options);
	}
	
	/**
	 * Import the learning path(s) into the system
	 *
	 */
	public function import_learning_object()
	{
		// Extract the zip file to the temporary directory
		$zip = Filecompression :: factory();
		$extracted_files_dir = $zip->extract_file($this->get_learning_object_file_property('tmp_name'));
	
		// Extract the xml file to an array
		$manifest_file = $extracted_files_dir . '/imsmanifest.xml';
		$xml_data = $this->extract_xml_file($manifest_file);
		
		// Move content from zip file to files/scorm/{user_id}/{scorm_package_identifier}/
		$scorm_path = Path :: get(SYS_SCORM_PATH) . $this->get_user()->get_id() . '/' . $xml_data['identifier'] . '/';
		FileSystem :: move_file($extracted_files_dir, $scorm_path);
		
		// Read through the resources list and determine the correct paths
		$base_path = $xml_data['xml:base'];
		$resources_base_path = $xml_data['resources']['xml:base'];
		$resources_path = $this->get_user()->get_id() . '/' . $xml_data['identifier'] . '/' . $base_path . $resources_base_path;
		$resources_list = $this->build_resources_list($xml_data['resources']['resource'], $resources_path);

		// Build the organizations tree
		$this->build_organizations($xml_data['organizations']['organization'], $resources_list);
		
		// Remove the temporary files
		FileSystem :: remove($extracted_files_dir);
		
	}
	
	/**
	 * Build the resources list from the SCORM resources. 
	 * This method will be used to place the correct path in the scorm items
	 *
	 * @param Array of Strings $resources - SCORM resources
	 * @return Array of Strings - The needed resources
	 */
	private function build_resources_list($resources, $resources_path)
	{
		$resources_list = array();
		
		foreach($resources as $resource)
		{
			if($resource['href'])
				$resources_list[$resource['identifier']] = $resources_path . $resource['xml:base'] . $resource['href'];
		}
		
		return $resources_list;
	}
	
	/*
	 * Build the learning path list from the SCORM organizations
	 * @param Array of Strings - SCORM organizations
	 */
	private function build_organizations($organizations, $resources_list)
	{
		foreach($organizations as $organization)
		{
			$learning_path = $this->create_learning_path($organization['title']);
			$this->build_items($organization['item'], $resources_list, $learning_path);
		}
	}
	
	/**
	 * Recursive method to built the items list. When child items are found, a sub learning path has to be created
	 * and the children must be processed
	 *
	 * @param Array of Strings $items - The items list
	 * @param Array of Strings $resources_list - The resources list
	 * @param LearningPath $parent_learning_path - The parent learning path
	 */
	private function build_items($items, $resources_list, $parent_learning_path)
	{
		foreach($items as $item)
		{
			if($item['item'])
			{
				$sub_learning_path = $this->create_learning_path($item['title']);
				$this->build_items($item['item'], $resources_list, $sub_learning_path);
				$this->add_sub_learning_path_to_learning_path($parent_learning_path, $sub_learning_path);
			}
			else
			{
				$scorm_item = $this->create_scorm_item($item, $resources_list[$item['identifierref']]);
				$this->add_scorm_item_to_learning_path($scorm_item, $parent_learning_path);
			}
		}
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
	private function create_scorm_item($item, $path)
	{
		$scorm_item = AbstractLearningObject :: factory('scorm_item');
		$scorm_item->set_title($item['title']);
		$scorm_item->set_description($item['title']);
		$scorm_item->set_parent_id($this->get_category());
		$scorm_item->set_owner_id($this->get_user()->get_id());
		$scorm_item->set_path($path);
		
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