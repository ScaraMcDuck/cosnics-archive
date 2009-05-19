<?php
/**
 * @package application.lib.profiler
 */
require_once Path :: get_application_library_path(). 'repo_viewer/repo_viewer.class.php';
require_once Path :: get_repository_path(). 'lib/abstract_learning_object.class.php';

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class ComplexRepoViewer extends RepoViewer
{
	/**
	 * The default learning objects, which are used for form defaults.
	 */
	
	function ComplexRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE)
	{
		parent :: __construct($parent, $types, $mail_option, $maximum_select);
		$this->set_repo_viewer_actions(array ('creator','browser', 'finder'));
	}
	
	function redirect_complex($type)
	{
		return false;
	}
	
	function parse_input()
	{
		$this->parse_input_from_table();
	}
}
?>