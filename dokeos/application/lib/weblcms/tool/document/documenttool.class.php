<?php
/**
 * $Id$
 * Document tool - list renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
require_once dirname(__FILE__).'/../../../../../common/filecompression/filecompression.class.php';
/**
 * This tool allows a user to publish documents in his or her course.
 */
class DocumentTool extends RepositoryTool
{
	const ACTION_DOWNLOAD = 'download';
	const ACTION_ZIP_AND_DOWNLOAD = 'zipanddownload';
	/*
	 * Inherited.
	 */
	function run()
	{
		if (isset($_GET['documenttoolmode']))
		{
			$_SESSION['documenttoolmode'] = $_GET['documenttoolmode'];
		}
		if( isset($_GET['admin']) && $_GET['admin'] == 0)
		{
			$_SESSION['documenttoolmode'] = 0;
		}

		if($this->is_allowed(ADD_RIGHT))
		{
			$html[] = '<ul style="list-style: none; padding: 0; margin: 0 0 1em 0">';
			$i = 0;
			$options['browser'] = 'BrowserTitle';
			$options['publish'] = 'Publish';
			$options['category'] = 'ManageCategories';
			foreach ($options as $key => $title)
			{
				$current = ($_SESSION['documenttoolmode'] == $i);
				$html[] =  '<li style="display: inline; margin: 0 1ex 0 0; padding: 0">';
				if (!$current)
				{
					$html[] =   '<a href="' . $this->get_url(array('documenttoolmode' => $i), true) . '">';
				}
				$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/'.$key.'.gif" alt="'.get_lang($title).'" style="vertical-align:middle;"/> ';
				$html[] =   get_lang($title);
				if (!$current)
				{
					$html[] =  '</a>';
				}
				$html[] =  '</li>';
				$i++;
			}
			if($_SESSION['documenttoolmode'] == 0)
			{
				$download_parameters[RepositoryTool::PARAM_ACTION] = self::ACTION_ZIP_AND_DOWNLOAD;
				$html[] =  '<li style="display: inline; margin: 0 1ex 0 0; padding: 0">';
				$html[] =   '<a href="' . $this->get_url($download_parameters) . '">';
				$html[] = '<img src="'.api_get_path(WEB_CODE_PATH).'/img/save.png" alt="'.get_lang('Download').'" style="vertical-align:middle;"/> ';
				$html[] =   get_lang('Download');
				$html[] =  '</a>';
				$html[] =  '</li>';
			}
			$html[] =  '</ul>';
		}
		$html[] = $this->perform_requested_actions();
		switch ($_SESSION['documenttoolmode'])
		{
			case 2:
				require_once dirname(__FILE__).'/../../learningobjectpublicationcategorymanager.class.php';
				$catman = new LearningObjectPublicationCategoryManager($this, 'document');
				$html[] =  $catman->as_html();
				break;
			case 1:
				require_once dirname(__FILE__).'/../../learningobjectpublisher.class.php';
				$pub = new LearningObjectPublisher($this, 'document');
				$html[] =  $pub->as_html();
				break;
			default:
				require_once dirname(__FILE__).'/documentbrowser.class.php';
				$browser = new DocumentBrowser($this);
				$html[] =  $browser->as_html();
		}
		$this->display_header();
		echo implode("\n",$html);
		$this->display_footer();
	}
	function perform_requested_actions()
	{
		$action = $_GET[RepositoryTool :: PARAM_ACTION];
		if( isset($action))
		{
			$datamanager = WeblcmsDataManager :: get_instance();
			switch($action)
			{
				case self::	ACTION_DOWNLOAD:
					$publication_id = $_GET[RepositoryTool :: PARAM_PUBLICATION_ID];
					$publication = $datamanager->retrieve_learning_object_publication($publication_id);
					$document = $publication->get_learning_object();
					$document->send_as_download();
					return '';
				case self::	ACTION_ZIP_AND_DOWNLOAD:
					$parent = $this->get_parent();
					$category_id = $parent->get_parameter(Weblcms::PARAM_CATEGORY);
					$category_folder_mapping = $this->create_folder_structure($category_id);
					$archive_file = $this->create_document_archive($category_folder_mapping);
					$archive_url = api_get_path(WEB_CODE_PATH).'../'.str_replace(DIRECTORY_SEPARATOR,'/',str_replace(realpath(api_get_path(SYS_PATH)),'',$archive_file));
					return Display::display_normal_message('<a href="'.$archive_url.'">'.get_lang('Download').'</a>',true);
				default:
					return parent::perform_requested_actions();
			}
		}
	}
	/**
	 * This function will create an archive file containing all contents of the
	 * given categories (only those visible for the current user). After the
	 * archive is created, the target folder will be deleted.
	 * @param array $category_folder_mapping
	 */
	private function create_document_archive($category_folder_mapping)
	{
		$dm = WeblcmsDataManager :: get_instance();
		if($this->is_allowed(EDIT_RIGHT))
		{
			$user_id = null;
			$groups = null;
		}
		else
		{
			$user_id = $this->get_user_id();
			$groups = $this->get_groups();
		}
		$target_path = current($category_folder_mapping);
		foreach($category_folder_mapping as $category_id => $dir)
		{
			$publications = $dm->retrieve_learning_object_publications($this->get_course_id(), $category_id, $user_id, $groups);
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
		return $archive_file;
	}
	/**
	 * Creates a folder structure from the given categories.
	 * @param array|int $categories
	 * @param array $category_folder_mapping
	 * @param $path
	 * @return array An array mapping the category id to the folder.
	 */
	private function create_folder_structure($categories,&$category_folder_mapping = array(), $path = null)
	{
		if(is_null($path))
		{
			$path = realpath(api_get_path(SYS_CODE_PATH).'../files/temp');
			$path = Filesystem::create_unique_name($path.'/weblcms_document_download_'.$this->get_parent()->get_course_id());
			$category_folder_mapping[$categories] = $path;
			Filesystem::create_dir($path);
			$parent = $this->get_parent();
			$course = $parent->get_course_id();
			$tool = $parent->get_parameter(Weblcms :: PARAM_TOOL);
			$categories = WeblcmsDataManager :: get_instance()->retrieve_learning_object_publication_categories($course, $tool,$categories);
		}
		foreach($categories as $index => $category_info)
		{
			$category = $category_info['obj'];
			$category_path = Filesystem::create_unique_name($path.'/'.$category->get_title());
			$category_folder_mapping[$category->get_id()] = $category_path;
			Filesystem::create_dir($category_path);
			$this->create_folder_structure($category_info['sub'],$category_folder_mapping,$category_path);
		}
		return $category_folder_mapping;
	}
}
?>