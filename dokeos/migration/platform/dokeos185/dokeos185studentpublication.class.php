<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a Dokeos185 student_publication
 *
 * @author Sven Vanpoucke
 */
class Dokeos185StudentPublication
{
	/**
	 * Dokeos185StudentPublication properties
	 */
	const PROPERTY_ID = 'id';
	const PROPERTY_URL = 'url';
	const PROPERTY_TITLE = 'title';
	const PROPERTY_DESCRIPTION = 'description';
	const PROPERTY_AUTHOR = 'author';
	const PROPERTY_ACTIVE = 'active';
	const PROPERTY_ACCEPTED = 'accepted';
	const PROPERTY_POST_GROUP_ID = 'post_group_id';
	const PROPERTY_SENT_DATE = 'sent_date';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185StudentPublication object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185StudentPublication($defaultProperties = array ())
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
		return array (SELF :: PROPERTY_ID, SELF :: PROPERTY_URL, SELF :: PROPERTY_TITLE, SELF :: PROPERTY_DESCRIPTION, SELF :: PROPERTY_AUTHOR, SELF :: PROPERTY_ACTIVE, SELF :: PROPERTY_ACCEPTED, SELF :: PROPERTY_POST_GROUP_ID, SELF :: PROPERTY_SENT_DATE);
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
	 * Returns the id of this Dokeos185StudentPublication.
	 * @return the id.
	 */
	function get_id()
	{
		return $this->get_default_property(self :: PROPERTY_ID);
	}

	/**
	 * Returns the url of this Dokeos185StudentPublication.
	 * @return the url.
	 */
	function get_url()
	{
		return $this->get_default_property(self :: PROPERTY_URL);
	}

	/**
	 * Returns the title of this Dokeos185StudentPublication.
	 * @return the title.
	 */
	function get_title()
	{
		return $this->get_default_property(self :: PROPERTY_TITLE);
	}

	/**
	 * Returns the description of this Dokeos185StudentPublication.
	 * @return the description.
	 */
	function get_description()
	{
		return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
	}

	/**
	 * Returns the author of this Dokeos185StudentPublication.
	 * @return the author.
	 */
	function get_author()
	{
		return $this->get_default_property(self :: PROPERTY_AUTHOR);
	}

	/**
	 * Returns the active of this Dokeos185StudentPublication.
	 * @return the active.
	 */
	function get_active()
	{
		return $this->get_default_property(self :: PROPERTY_ACTIVE);
	}

	/**
	 * Returns the accepted of this Dokeos185StudentPublication.
	 * @return the accepted.
	 */
	function get_accepted()
	{
		return $this->get_default_property(self :: PROPERTY_ACCEPTED);
	}

	/**
	 * Returns the post_group_id of this Dokeos185StudentPublication.
	 * @return the post_group_id.
	 */
	function get_post_group_id()
	{
		return $this->get_default_property(self :: PROPERTY_POST_GROUP_ID);
	}

	/**
	 * Returns the sent_date of this Dokeos185StudentPublication.
	 * @return the sent_date.
	 */
	function get_sent_date()
	{
		return $this->get_default_property(self :: PROPERTY_SENT_DATE);
	}

	function is_valid($courses)
	{
		$course = $courses[0];
		$this->item_property = self :: $mgdm->get_item_property($course->get_db_name(),'work',$this->get_id());	
		
		if(!$this->get_id() || !$this->get_url() ||
			!$this->item_property->get_insert_date())
		{		 
			self :: $mgdm->add_failed_element($this->get_id(),
				$course->get_db_name() . '.student_publication');
			return false;
		}
		return true;
	}
	
	function convert_to_lcms($array)
	{
		$new_user_id = self :: $mgdm->get_id_reference($this->item_property->get_insert_user_id(),'user_user');	
		$course = $array['course'];
		$new_course_code = self :: $mgdm->get_id_reference($course->get_code(),'weblcms_course');	
		
		if(!$new_user_id)
		{
			$new_user_id = self :: $mgdm->get_owner($new_course_code);
		}
		
		$new_path = $new_user_id . '/';
		$old_rel_path = 'courses/' . $course->get_code() . '/' . $this->get_url();

		$new_rel_path = 'files/repository/' . $new_path;
		$filename = basename($this->get_url());
		
		$lcms_document = null;
		
		if(!self :: $files[$new_user_id][md5_file(self :: $mgdm->append_full_path(false,$old_rel_path . $filename))])
		{
			
			$filename = iconv("UTF-8", "ISO-8859-1", $filename);
			$old_rel_path = iconv("UTF-8", "ISO-8859-1", $old_rel_path);

			// Move file to correct directory
			//echo($old_rel_path . "\t" . $new_rel_path . "\t" . $filename . "\n");

			$file = self :: $mgdm->move_file($old_rel_path, $new_rel_path, 
				$filename);

			if($file)
			{
				//document parameters
				$lcms_document = new Document();
	
				$lcms_document->set_filesize($this->get_filesize());
				if($this->get_title())
					$lcms_document->set_title($this->get_title());
				else
					$lcms_document->set_title($filename);
				$lcms_document->set_description('...');
				
				$lcms_document->set_owner_id($new_user_id);
				$lcms_document->set_creation_date(self :: $mgdm->make_unix_time($this->item_property->get_insert_date()));
				$lcms_document->set_modification_date(self :: $mgdm->make_unix_time($this->item_property->get_lastedit_date()));
				$lcms_document->set_path($new_path . $file);
				$lcms_document->set_filename($file);
				
				// Category for announcements already exists?
				$lcms_category_id = self :: $mgdm->get_parent_id($new_user_id, 'category',
					Translation :: get_lang('dropbox'));
				if(!$lcms_category_id)
				{
					//Create category for tool in lcms
					$lcms_repository_category = new Category();
					$lcms_repository_category->set_owner_id($new_user_id);
					$lcms_repository_category->set_title(Translation :: get_lang('dropboxes'));
					$lcms_repository_category->set_description('...');
			
					//Retrieve repository id from dropbox
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
			
				if($this->item_property->get_visibility() == 2)
					$lcms_document->set_state(1);
				
				//create document in database
				$lcms_document->create_all();
				
				self :: $files[$new_user_id][md5_file(self :: $mgdm->append_full_path(true,$new_rel_path . $file))] = $lcms_document->get_id();
			}
			
		}
		else
		{
			$lcms_document = new LearningObject();
			$id = self :: $files[$new_user_id][md5_file(self :: $mgdm->append_full_path(false,$old_rel_path . $this->get_filename()))];
			$lcms_document->set_id($id);
		}
		/*	
		//publication
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

	static function get_all($parameters = array())
	{
		self :: $mgdm = $parameters['mgdm'];

		if($array['del_files'] =! 1)
			$tool_name = 'work';
		
		$coursedb = $array['course'];
		$tablename = 'student_publication';
		$classname = 'Dokeos185StudentPublication';
			
		return self :: $mgdm->get_all($coursedb, $tablename, $classname, $tool_name);	
	}
}

?>