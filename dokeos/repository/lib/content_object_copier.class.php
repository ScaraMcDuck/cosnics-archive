<?php

/**
 * Class that makes it possible to copy a content object between two user repositories. This class takes the following things in account:
 * Simple learning object
 * The children of a complex learning object
 * The included learning objects
 * The attached learning objects
 * The physical files (documents, hotpotatoes, scorm)
 * The references of LearningPathItem & PortfolioItem
 * The LearningPath prerequisites (only for dokeos learning paths)
 * Links to other files in a description field
 * 
 * @author Sven Vanpoucke
 */
class ContentObjectCopier
{
	/**
	 * The repository data manager
	 *
	 * @var RepositoryDataManager
	 */
	private $rdm;
	
	/**
	 * The target repository
	 *
	 * @var Int
	 */
	private $target_repository;
	
	/**
	 * Counter to count the items that failed while copying
	 *
	 * @var Int
	 */
	private $failed;
	
	/**
	 * Array of already copied content objects in order to not copy content objects twice
	 *
	 * @var ContentObject[]
	 */
	private $created_content_objects;
	
	/**
	 * Constructor 
	 * Initialize the repository data manager
	 * Set the target repository
	 *
	 * @param Int $target_repository
	 */
	function ContentObjectCopier($target_repository = 0)
	{
		$this->rdm = RepositoryDataManager :: get_instance();
		$this->target_repository = $target_repository;	
	}
	
	/**
	 * Copy a content object to the target repository
	 *
	 * @param Int $co
	 * @return Int the amount of content objects that where failed to copy
	 */
	function copy_content_object($co)
	{
		$this->failed = 0;
		
		$this->create_content_object($co);
   		exit();
   		return $this->failed;
	}
	
	/**
	 * Create a content object in the target repository
	 *
	 * @param ContentObject $co
	 * @return Int the id of the new created content object
	 */
	private function create_content_object($co)
	{
		$old_co_id = $co->get_id();
		$old_user_id = $co->get_owner_id();

		if(array_key_exists($old_co_id, $this->created_content_objects))
			return $this->created_content_objects[$old_co_id]->get_id();
	
		// Retrieve includes and attachments
		$includes = $co->get_included_content_objects();
		$attachments = $co->get_attached_content_objects();
		
		// Replace some properties
		$co->set_owner_id($this->target_repository);
   		$co->set_parent_id(0);
   		
   		// Create object
   		if(!$co->create())
   		{
   			$this->failed++;
   		}
   		
   		// Add object to created content objects
   		$this->created_content_objects[$old_co_id] = $co;
   		
   		// Process the children
   		if($co->is_complex_content_object())
   		{
   			$this->copy_complex_children($old_co_id, $co->get_id());
   		}
   		
   		// Process the included items and the attachments
   		$this->copy_includes($co, $includes);
   		$this->copy_attachments($co, $attachments);
   		
   		// Process the physical files
		$this->copy_files($co, $old_user_id);
   		
   		return $co->get_id();
	}
	
	/**
	 * Copy the children of a content object (both items and wrappers)
	 *
	 * @param Int $old_parent_id
	 * @param Int $new_parent_id
	 */
	private function copy_complex_children($old_parent_id, $new_parent_id)
	{
		$condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $old_parent_id, ComplexContentObjectItem :: get_table_name());
		$items = $this->rdm->retrieve_complex_content_object_items($condition);
		while($item = $items->next_result())
		{
			$co = $this->rdm->retrieve_content_object($item->get_ref());
			$co_id = $this->create_content_object($co);
			
			$nitem = new ComplexContentObjectItem();
			$nitem->set_user_id($this->target_repository);
			$nitem->set_display_order($item->get_display_order());
			$nitem->set_parent($new_parent_id);
			$nitem->set_ref($co_id);
			$nitem->create();
			
			$this->copy_complex_children($item->get_ref(), $co_id);
			
		}
	}
	
	/**
	 * Copy the included content objects
	 *
	 * @param ContentObject $co
	 * @param ContentObject[]
	 */
	private function copy_includes($co, $includes)
	{
		foreach($includes as $include)
		{
			$new_include_id = $this->create_content_object($include);
			$co->include_content_object($new_include_id);
		}
	}
	
	/**
	 * Copy the attached content objects
	 *
	 * @param ContentObject $co
	 * @param ContentObject[]
	 */
	private function copy_attachments($co, $attachments)
	{
		foreach($attachments as $attachment)
		{
			$new_attachment_id = $this->create_content_object($attachment);
			$co->attach_content_object($new_attachment_id);
		}
	}
	
	/**
	 * Copy the physical files
	 * @param ContentObject $co;
	 */
	private function copy_files($co, $old_user_id)
	{
		$type = $co->get_type();
		switch($type)
		{
			case 'document': 
				return $this->copy_document_files($co);
			case 'hotpotatoes': 
				return $this->copy_hotpotatoes_files($co, $old_user_id);
			case 'learning_path':
				if($co->get_version() == 'SCORM1.2' || $co->get_version() == 'SCORM2004')
				{
					return $this->copy_scorm_files($co, $old_user_id);
				}
			default:
				return;
		}
	}
	
	/**
	 * Copy the files from the content object type document
	 *
	 * @param Document $co
	 */
	private function copy_document_files($co)
	{
		$base_path = Path :: get(SYS_REPO_PATH);
		$new_path = $this->target_repository . '/' . Text :: char_at($co->get_hash(), 0);
		$new_full_path = $base_path . $new_path;
		Filesystem :: create_dir($new_full_path);
		
		$new_hash = Filesystem :: create_unique_name($new_full_path, $co->get_hash()); 
		$new_full_path .= '/' . $new_hash;
		
		Filesystem :: copy_file($co->get_full_path(), $new_full_path);
		
		$co->set_hash($new_hash);
		$co->set_path($new_path . '/' . $new_hash);
		$co->update();
	}
	
	/**
	 * Copy the files from the content object type hotpotatoes
	 *
	 * @param Hotpotatoes $co
	 */
	private function copy_hotpotatoes_files($co, $old_user_id)
	{
		$filename = basename($co->get_path());
		$base_path = Path :: get(SYS_HOTPOTATOES_PATH) . $this->target_repository . '/';
		
		$new_path = Filesystem :: create_unique_name($base_path, dirname($co->get_path()));
		$new_full_path = $base_path . $new_path;
		Filesystem :: create_dir($new_full_path);

		Filesystem :: recurse_copy(Path :: get(SYS_HOTPOTATOES_PATH) . $old_user_id . '/' . dirname($co->get_path()), $new_full_path, false);
		
		$co->set_path($new_path . '/' . $filename);
		$co->update();
	}
	
	/**
	 * Copy the files from the content object type learning path
	 *
	 * @param LearningPath $co
	 */
	private function copy_scorm_files($co, $old_user_id)
	{
		$base_path = Path :: get(SYS_SCORM_PATH) . $this->target_repository . '/';
		
		$new_folder = Filesystem :: create_unique_name($base_path, $co->get_path());
		$new_full_path = $base_path . $new_folder;
		Filesystem :: create_dir($new_full_path);

		Filesystem :: recurse_copy(Path :: get(SYS_SCORM_PATH) . $old_user_id . '/' . $co->get_path(), $new_full_path, false);
		
		$co->set_path($new_folder);
		$co->update();
	}
}

?>