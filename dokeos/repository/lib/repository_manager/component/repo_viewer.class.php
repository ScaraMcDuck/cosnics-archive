<?php
/**
 * @package application.weblcms.weblcms_manager.component
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once Path :: get_application_library_path() . 'repo_viewer/repo_viewer.class.php';

/**
 * Weblcms component allows the user to manage course categories
 */
class RepositoryManagerRepoViewerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
       	Display :: small_header();
       	$object = Request :: get('object');
		$pub = new RepoViewer($this, 'document', false, RepoViewer :: SELECT_SINGLE, array(), true, false);
       	if(!isset($object))
	   	{
			echo $pub->as_html();
		}
		else
		{
			$html[] = '<script language="javascript">';
			$html[] = 'window.parent.object_selected(' . $object . ');';
			$html[] = '</script>';
			echo implode("\n", $html);
		}
	}
}
?>