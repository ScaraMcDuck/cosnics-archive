<?php

/**
 * @package migration.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importdocument.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object/document/document.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once 'dokeos185itemproperty.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublicationcategory.class.php';

/**
 * This class represents an old Dokeos 1.8.5 document
 *
 * @author David Van Wayenbergh
 */
 
class Dokeos185Document extends Import
{
	private static $mgdm;
	private $item_property;
	

	/**
	 * document properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_PATH = 'path';
	const PROPERTY_TITLE = 'title';
 	const PROPERTY_SIZE = 'size';
 	const PROPERTY_COMMENT = 'comment';
 	const PROPERTY_FILETYPE = 'filetype';
	
	/**
	 * Default properties of the document object, stored in an associative
	 * array.
	 */
	private $defaultProperties;
	
	/**
	 * Creates a new document object.
	 * @param array $defaultProperties The default properties of the document
	 *                                 object. Associative array.
	 */
	function Dokeos185Document($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Gets a default property of this document object by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}
	
	/**
	 * Gets the default properties of this document.
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}
	
	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}
	
	/**
	 * Get the default properties of all documents.
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID,self :: PROPERTY_PATH,self :: PROPERTY_TITLE,
			self :: PROPERTY_SIZE,self :: PROPERTY_COMMENT, self :: PROPERTY_FILETYPE);
	}
	
	/**
	 * Sets a default property of this document by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}
	
	/**
	 * Checks if the given identifier is the name of a default document
	 * property.
	 * @param string $name The identifier.
	 * @return boolean True if the identifier is a property name, false
	 *                 otherwise.
	 */
	static function is_default_property_name($name)
	{
		return in_array($name, self :: get_default_property_names());
	}
	
	/**
	 * Returns the id of this document.
	 * @return int The id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}
	
	/**
	 * Returns the path of this document.
	 * @return String The path.
	 */
	function get_path()
	{
		return $this->get_default_property(self :: PROPERTY_PATH);
	}
	
	/**
	 * Returns the title of this document.
	 * @return String The title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}
	
	/**
	 * Returns the size of this document.
	 * @return int The size.
	 */
	function get_size()
	{
		return $this->get_default_property(self :: PROPERTY_SIZE);
	}
	
	/**
	 * Returns the comment of this document.
	 * @return String The comment.
	 */
	function get_comment()
	{
		return $this->get_default_property(self :: PROPERTY_COMMENT);
	}
	
	/**
	 * Returns the filetype of this document.
	 * @return String The filetype.
	 */
	function get_filetype()
	{
		return $this->get_default_property(self :: PROPERTY_FILETYPE);
	}
	
	/**
	 * Sets the id of this document.
	 * @param int $id The id.
	 */
	function set_id($id)
	{
		$this->set_default_property(self :: PROPERTY_ID, $id);
	}
	
	/**
	 * Sets the path of this document.
	 * @param String $path The $path.
	 */
	function set_path($path)
	{
		$this->set_default_property(self :: PROPERTY_PATH, $path);
	}
	
	/**
	 * Sets the title of this document.
	 * @param String $title The title.
	 */
	function set_title($title)
	{
		$this->set_default_property(self :: PROPERTY_TITLE, $title);
	}
	
	/**
	 * Sets the size of this document.
	 * @param String $size The size.
	 */
	function set_size($size)
	{
		$this->set_default_property(self :: PROPERTY_SIZE, $size);
	}
	
	/**
	 * Sets the comment of this document.
	 * @param String $comment The comment.
	 */
	function set_comment($comment)
	{
		$this->set_default_property(self :: PROPERTY_COMMENT, $comment);
	}
	
	/**
	 * Sets the filetype of this document.
	 * @param String $filetype The filetype.
	 */
	function set_filetype($filetype)
	{
		$this->set_default_property(self :: PROPERTY_FILETYPE, $filetype);
	}
	
	function is_valid_document($course)
	{
		$this->item_property = self :: $mgdm->get_item_property($course->get_db_name(),'document',$this->get_id());	
		
		if(!$this->get_id() || !$this->get_path() || !$this->get_filetype()
			|| $this->item_property->get_insert_user_id() == 0 || !$this->item_property->get_insert_date() ||
			self :: $mgdm->get_failed_element('dokeos_main.user', $this->item_property->get_insert_user_id() ))
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.document');
			return false;
		}
		return true;
	}
	
	function convert_to_new_document($course)
	{
		$new_user_id = self :: $mgdm->get_id_reference($this->item_property->get_insert_user_id(),'user_user');	
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');	
	
		//document parameters
		$lcms_document = new Document();

		$lcms_document->set_filesize($this->get_size());
		
		$pos = strpos($this->get_path(), $this->get_title());
		$filename = substr($this->get_path(), $pos);
		$old_path = substr($this->get_path(), 0, $pos);
		
		// Categories already exists?
		$file_split = array();
		$file_split = split('/', $old_path);
		
		array_shift($file_split);
		array_pop($file_split);
		
		$parent = 0;
		
		foreach($file_split as $cat)
		{
			$lcms_category_id = self :: $mgdm->publication_category_exist($cat, $new_course_code,
				'document',$parent);
			
			if(!$lcms_category_id)
			{
				//Create category for tool in lcms
				$lcms_category = new LearningObjectPublicationCategory();
				$lcms_category->set_title($cat);
				$lcms_category->set_course($new_course_code);
				$lcms_category->set_tool('document');
				$lcms_category->set_parent_category_id($parent);
				
				//Create category in database
				$lcms_category->create();
				$parent = $lcms_category->get_id();
			}
			else
			{
				$parent = $lcms_category_id;
			}
			
		}
		
		// Move file to correct directory
		
		$new_path = $new_user_id . $old_path;
		$old_rel_path = 'courses/' . $course->get_code() . '/document/'  . $old_path;

		$new_rel_path = 'files/repository/' . $new_path;
		
		$file = self :: $mgdm->move_file($old_rel_path, $new_rel_path, 
			$filename);
		
			
		if($file)
		{
			$lcms_document->set_title($this->get_title());
			$lcms_document->set_description('...');
			$lcms_document->set_comment($this->get_comment());
			
			$lcms_document->set_owner_id($new_user_id);
			$lcms_document->set_creation_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
			$lcms_document->set_modification_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
			$lcms_document->set_path($new_rel_path);
			$lcms_document->set_filename($filename);
			$lcms_document->set_parent_id($parent);
		
			if($this->item_property->get_visibility() == 2)
				$lcms_document->set_state(1);
			
			
			//create document in database
			$lcms_document->create_all();
			
			//publication
			if($this->item_property->get_visibility() <= 1) 
			{
				$publication = new LearningObjectPublication();
				
				$publication->set_learning_object($lcms_document);
				$publication->set_course_id($new_course_code);
				$publication->set_publisher_id($new_user_id);
				$publication->set_tool('document');
				$publication->set_category_id($parent);
				//$publication->set_from_date(self :: $mgdm->make_unix_time($this->item_property->get_start_visible()));
				//$publication->set_to_date(self :: $mgdm->make_unix_time($this->item_property->get_end_visible()));
				$publication->set_from_date(0);
				$publication->set_to_date(0);
				$publication->set_publication_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
				$publication->set_modified_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
				//$publication->set_modified_date(0);
				//$publication->set_display_order_index($this->get_display_order());
				$publication->set_display_order_index(0);
				$publication->set_email_sent(0);
				
				$publication->set_hidden($this->item_property->get_visibility() == 1?0:1);
						
			}
			
			//create publication in database
			$publication->create();
		}
		
		return $lcms_document;
	}
	
	static function get_all_documents($course, $mgdm)
	{
		self :: $mgdm = $mgdm;
		return self :: $mgdm->get_all_documents($course);
	}
}
?>
