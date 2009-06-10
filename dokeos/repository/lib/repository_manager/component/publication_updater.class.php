<?php
/**
 * @package repository.repositorymanager
 *
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component which provides functionality to update a
 * learning object publication.
 */
class RepositoryManagerPublicationUpdaterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$application = Request :: get(RepositoryManager :: PARAM_PUBLICATION_APPLICATION);
		$publication_id = Request :: get(RepositoryManager :: PARAM_PUBLICATION_ID);

		if (!empty ($application) && !empty ($publication_id))
		{
			$pub = $this->get_parent()->get_learning_object_publication_attribute($publication_id, $application);
			$latest_version = $pub->get_publication_object()->get_latest_version_id();

			$pub->set_publication_object_id($latest_version);
			$success = $pub->update();

			$this->redirect(Translation :: get($success ? 'PublicationUpdated' : 'PublicationUpdateFailed'), ($success ? false : true), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS));
		}
		else
		{
			$this->display_warning_page(htmlentities(Translation :: get('NoPublicationSelected')));
		}
	}
}
?>