<?php
/**
 * @package application.lib.profiler.repo_viewer
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/finder.class.php';
require_once dirname(__FILE__).'/learning_object_browser.class.php';
require_once Path :: get_library_path().'condition/and_condition.class.php';
require_once Path :: get_library_path().'condition/or_condition.class.php';
require_once Path :: get_library_path().'condition/pattern_match_condition.class.php';
require_once Path :: get_library_path().'html/formvalidator/FormValidator.class.php';
/**
 * This class represents a profiler repo_viewer component which can be used
 * to search for a certain learning object.
 */
 
class LearningObjectRepoViewerFinderComponent extends RepoViewerFinderComponent
{
	function LearningObjectRepoViewerFinderComponent($parent)
	{
		parent :: __construct($parent);
		$form = $this->get_form();
		$form->addElement('hidden', Application :: PARAM_APPLICATION);
		$form->addElement('hidden', WeblcmsManager :: PARAM_ACTION);
		$form->addElement('hidden', WeblcmsManager :: PARAM_COURSE);
		$form->addElement('hidden', WeblcmsManager :: PARAM_TOOL);
		$form->addElement('hidden', WeblcmsManager :: PARAM_TOOL_ACTION);
	}
}
?>