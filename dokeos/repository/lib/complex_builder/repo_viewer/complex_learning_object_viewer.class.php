<?php
/**
 * @package application.lib.profiler.repo_viewer
 */
require_once Path :: get_application_library_path() . 'repo_viewer/component/viewer.class.php';
require_once Path :: get_repository_path(). 'lib/repository_data_manager.class.php';
require_once Path :: get_repository_path(). 'lib/learning_object_display.class.php';
require_once Path :: get_library_path() . 'dokeos_utilities.class.php';

/**
 * This class represents a profile repo_viewer component which can be used
 * to preview a learning object in the learning object repo_viewer.
 */
class ComplexLearningObjectRepoViewerViewerComponent extends RepoViewerViewerComponent
{
}
?>