<?php
/**
 * $Id: editor.class.php 21345 2009-06-10 13:15:00Z MichaelKyndt $
 * @package repository.repositorymanager
 *
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
/**
 * Repository manager component to edit an existing learning object.
 */
class RepositoryManagerDocumentDownloaderComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$object_id = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_ID);
		if(!$object_id)
		{
			$this->display_header();
			$this->display_error_message(Translation :: get('NoContentObjectSelected'));
			$this->display_footer();
			exit();
		}
		
		
		$lo = $this->retrieve_content_object($object_id);
		if($lo->get_type() != 'document')
		{
			$this->display_header();
			$this->display_error_message(Translation :: get('ContentObjectMustBeDocument'));
			$this->display_footer();
			exit();
		}
		
		$lo->send_as_download();
	}
}
?>