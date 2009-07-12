<?php

require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';
require_once Path :: get_library_path() . 'filecompression/filecompression.class.php';

class DocumentToolZipAndDownloadComponent extends DocumentToolComponent
{
	private $action_bar;

	function run()
	{
		if(!$this->is_allowed(VIEW_RIGHT))
		{
			Display :: not_allowed();
			return;
		}
		$trail = new BreadcrumbTrail();
		$trail->add_help('courses document tool');
		$this->display_header($trail, true);
		$archive_url = $this->create_document_archive();
		echo Display :: normal_message('<a href="'.$archive_url.'">'.Translation :: get('Download').'</a>',true);
		$this->display_footer();
	}

	private function create_document_archive()
	{
		$parent = $this->get_parent();
		$category_id = $parent->get_parameter(WeblcmsManager :: PARAM_CATEGORY);
		if(!isset($category_id) || is_null($category_id) || strlen($category_id) == 0)
		{
			$category_id = 0;
		}
		$category_folder_mapping = $this->create_folder_structure($category_id);
		$datamanager = WeblcmsDataManager :: get_instance();
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$course_groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$course_groups = $this->get_course_groups();
		}
		$target_path = current($category_folder_mapping);
		foreach($category_folder_mapping as $category_id => $dir)
		{
			$conditions = array();
			$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
			$conditions[] = new EqualityCondition(LearningObjectPublication :: PROPERTY_TOOL, 'document');
			$conditions[] = new InCondition(LearningObjectPublication :: PROPERTY_CATEGORY_ID, $category_id);
			
			$access = array();
			$access[] = new InCondition('user', $user_id, $datamanager->get_database()->get_alias('learning_object_publication_user'));
			$access[] = new InCondition('course_group_id', $course_groups, $datamanager->get_database()->get_alias('learning_object_publication_course_group'));
			if (!empty($user_id) || !empty($course_groups))
			{
				$access[] = new AndCondition(array(new EqualityCondition('user', null, $datamanager->get_database()->get_alias('learning_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('learning_object_publication_course_group'))));
			}
			$conditions[] = new OrCondition($access);
			
			$subselect_condition = new EqualityCondition('type', 'document');
			$conditions[] = new SubselectCondition(LearningObjectPublication :: PROPERTY_LEARNING_OBJECT_ID, LearningObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(LearningObject :: get_table_name()), $subselect_condition);
			$condition = new AndCondition($conditions);
			
			$publications = $datamanager->retrieve_learning_object_publications_new($condition);
			while($publication = $publications->next_result())
			{
				$document = $publication->get_learning_object();
				$document_path = $document->get_full_path();
				$archive_file_location = $dir.'/'.Filesystem::create_unique_name($dir,$document->get_filename());
				Filesystem::copy_file($document->get_full_path(),$archive_file_location);
			}
		}
		$compression = FileCompression::factory();
		$archive_file = $compression->create_archive($target_path);
		Filesystem::remove($target_path);
		$archive_url = Path :: get(WEB_PATH).str_replace(DIRECTORY_SEPARATOR,'/',str_replace(realpath($this->get_parent()->get_path(SYS_PATH)),'',$archive_file));
		return $archive_url;
	}
	/**
	 * Creates a folder structure from the given categories.
	 * @param array|int $categories
	 * @param array $category_folder_mapping
	 * @param $path
	 * @return array An array mapping the category id to the folder.
	 */
	private function create_folder_structure($parent_cat,&$category_folder_mapping = array(), $path = null)
	{
		if(is_null($path))
		{
			$path = realpath(Path :: get(SYS_TEMP_PATH)); //dump($path);
			$path = Filesystem::create_unique_name($path.'/weblcms_document_download_'.$this->get_parent()->get_course_id());
			$category_folder_mapping[$parent_cat] = $path;
			Filesystem::create_dir($path);
			$parent = $this->get_parent();
			$course = $parent->get_course_id();
			$tool = $parent->get_parameter(WeblcmsManager :: PARAM_TOOL);

			$conditions[] = new EqualityCondition('course',$course);
			$conditions[] = new EqualityCondition('tool',$tool);
			$conditions[] = new EqualityCondition('parent',$parent_cat);
			$condition = new AndCondition($conditions); //dump($condition);

			$categories = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($condition);

			while($category = $categories->next_result())
			{
				$category_path = Filesystem::create_unique_name($path.'/'.$category->get_name());
				$category_folder_mapping[$category->get_id()] = $category_path;
				Filesystem::create_dir($category_path);
				$this->create_folder_structure($category->get_id(),$category_folder_mapping,$category_path);
			}
		}
		return $category_folder_mapping;
	}
}

?>