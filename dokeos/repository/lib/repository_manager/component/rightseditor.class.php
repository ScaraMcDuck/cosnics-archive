<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
/**
 * Repository manager component to edit the rights for the learning objects in
 * the repository.
 */
class RepositoryManagerRightsEditorComponent extends RepositoryManagerComponent
{
	function run()
	{
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('EditRights')));
		$this->display_header($breadcrumbs);
		//TODO: Implementation (connect to existing Roles&Rights stuff)
		echo '<p>'.htmlentities(get_lang('NotAvailable')).'</p>';
		$this->display_footer();
	}
}
?>