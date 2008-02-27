<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class RepositoryManagerRightsEditorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => Translation :: get_lang('EditRights')));
		$this->display_header($breadcrumbs);
		//TODO: Implementation (connect to existing Roles&Rights stuff)
		echo '<p>'.htmlentities(Translation :: get_lang('NotAvailable')).'</p>';
		$this->display_footer();
	}
}
?>