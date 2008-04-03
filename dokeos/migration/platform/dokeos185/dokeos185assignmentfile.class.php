<?php 
/**
 * migration.lib.platform.dokeos185
 */

require_once dirname(__FILE__).'/../../lib/import/importassignmentfile.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learning_object/document/document.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublication.class.php';
require_once dirname(__FILE__) . '/../../../application/lib/weblcms/learningobjectpublicationcategory.class.php';
require_once dirname(__FILE__).'/../../../repository/lib/learningobject.class.php';

/**
 * This class presents a Dokeos185 assignment_file
 *
 * @author Sven Vanpoucke
 */
class Dokeos185AssignmentFile
{
	/**
	 * Dokeos185AssignmentFile properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_ASSIGNMENT_ID = 'assignment_id';
	const PROPERTY_DOC_PATH = 'doc_path';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185AssignmentFile object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185AssignmentFile($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (self :: PROPERTY_ID, self :: PROPERTY_ASSIGNMENT_ID, self :: PROPERTY_DOC_PATH);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the id of this Dokeos185AssignmentFile.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the assignment_id of this Dokeos185AssignmentFile.
	 * @return the assignment_id.
	 */
	function get_assignment_id()
	{
		return $this->get_default_property(self :: PROPERTY_ASSIGNMENT_ID);
	}

	/**
	 * Returns the doc_path of this Dokeos185AssignmentFile.
	 * @return the doc_path.
	 */
	function get_doc_path()
	{
		return $this->get_default_property(self :: PROPERTY_DOC_PATH);
	}

	function is_valid_document($course)
	{
		$filename = $this->get_doc_path();
		$old_rel_path = 'courses/' . $course->get_directory() . '/assignment/assig_'  . $this->get_assignment_id() . '/';

		$filename = iconv("UTF-8", "ISO-8859-1", $filename);
		$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

		if(!$this->get_assignment_id() || !$this->get_doc_path()|| !file_exists(self :: $mgdm->append_full_path(false,$old_rel_path . $filename)) )
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.assignment_file');
			return false;
		}
		return true;
	}
	
	function convert_to_new_document($course)
	{
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');	
		$new_user_id = self :: $mgdm->get_owner($new_course_code);
		
		$filename = $this->get_doc_path();
		$new_path = $new_user_id . '/';
		$old_rel_path = 'courses/' . $course->get_directory() . '/assignment/assig_'  . $this->get_assignment_id() . '/';

		$new_rel_path = 'files/repository/' . $new_path;
		
		$lcms_document = null;

		$filename = iconv("UTF-8", "ISO-8859-1", $filename);
		$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

		$document_md5 = md5_file(self :: $mgdm->append_full_path(false,$old_rel_path . $filename)); 
		$document_id = self :: $mgdm->get_document_from_md5($new_user_id,$document_md5);
		
		if(!$document_id)
		{
			$file = self :: $mgdm->move_file($old_rel_path, $new_rel_path, 
				$filename);

			if($file)
			{
				//document parameters
				$lcms_document = new Document();
				$lcms_document->set_title($filename);
				$lcms_document->set_description('...');
				
				$lcms_document->set_owner_id($new_user_id);
				
				$lcms_document->set_path($new_path . $file);
				$lcms_document->set_filename($file);
				
				// Category for announcements already exists?
				$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
					Translation :: get_lang('assignment'));
				if(!$lcms_category_id)
				{
					//Create category for tool in lcms
					$lcms_repository_category = new Category();
					$lcms_repository_category->set_owner_id($new_user_id);
					$lcms_repository_category->set_title(Translation :: get_lang('assignment'));
					$lcms_repository_category->set_description('...');
			
					//Retrieve repository id from course
					$repository_id = self :: $mgdm->get_parent_id($new_user_id, 
						'category', Translation :: get_lang('MyRepository'));
					$lcms_repository_category->set_parent_id($repository_id);
					
					//Create category in database
					$lcms_repository_category->create();
					
					$lcms_document->set_parent_id($lcms_repository_category->get_id());
				}
				else
				{
					$lcms_document->set_parent_id($lcms_category_id);	
				}
				
				//create document in database
				$lcms_document->create();
			
				self :: $mgdm->add_file_md5($new_user_id, $lcms_document->get_id(), $document_md5);
			}
			else
			{ 
				$document_id = self :: $mgdm->get_document_id($new_rel_path . $filename, $new_user_id);
				if($document_id)
				{
					$lcms_document = new LearningObject();
					$lcms_document->set_id($document_id);
				}
			}
			
		}
		else
		{
			$lcms_document = new LearningObject();
		}
			
		//publication
		/*
		if($this->item_property->get_visibility() <= 1 && $lcms_document) 
		{
			
			// Categories already exists?
			$file_split = array();
			$file_split = split('/', $old_path);
			
			array_shift($file_split);
			array_pop($file_split);
			
			$parent = 0;
			
			foreach($file_split as $cat)
			{
				$lcms_category_id = self :: $mgdm->publication_category_exist($cat, $new_course_code,
					'assignment',$parent);
				
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
			
			$end_time_cat = Logger :: get_microtime();
			$passedtime_categories = $end_time_cat - $start_time_cat;
		
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
			
			//create publication in database
			$publication->create();
		}
		*/	
		return $lcms_document; 
	}
	
	static function get_all($parameters)
	{
		self :: $mgdm = $parameters['mgdm'];
		
		$coursedb = $parameters['course']->get_db_name();
		$tablename = 'assignment_file';
		$classname = 'Dokeos185AssignmentFile';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
}

?>