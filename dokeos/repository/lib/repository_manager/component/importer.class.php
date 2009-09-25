<?php
/**
 * $Id: deleter.class.php 15420 2008-05-26 17:34:32Z Scara84 $
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../import/content_object_import.class.php';
require_once dirname(__FILE__).'/../../content_object_import_form.class.php';
/**
 * Repository manager component which provides functionality to delete a
 * learning object from the users repository.
 */
class RepositoryManagerImporterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail(false);
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ContentObjectImport')));
		$trail->add_help('repository importer');
		
		$import_form = new ContentObjectImportForm('import', 'post', $this->get_url(), $this->get_parameter(RepositoryManager :: PARAM_CATEGORY_ID), $this->get_user());

		if ($import_form->validate())
		{
			$succes = $import_form->import_content_object();

			$message = $succes ? 'ContentObjectImported' : 'ContentObjectNotImported';
			$this->redirect(Translation :: get($message), !$succes, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_LEARNING_OBJECTS));
		}
		else
		{
			$this->display_header($trail, false, true);
			$import_form->display();
			$this->display_footer();
		}
	}
}
?>