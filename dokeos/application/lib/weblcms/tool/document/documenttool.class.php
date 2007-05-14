<?php
/**
 * $Id$
 * Document tool - list renderer
 * @package application.weblcms.tool
 * @subpackage document
 */
require_once dirname(__FILE__).'/../repositorytool.class.php';
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
					// TODO: Implement zip & download action
					return Display::display_warning_message('TODO',true);
				default:
					return parent::perform_requested_actions();
			}
		}
	}
}
?>