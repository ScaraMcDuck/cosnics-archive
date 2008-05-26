<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
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
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('EditRights')));
		
		$this->display_header($trail);
		//TODO: Implementation (connect to existing Roles&Rights stuff)
		echo '<p>'.htmlentities(Translation :: get('NotAvailable')).'</p>';
		$this->display_footer();
	}
}
?>