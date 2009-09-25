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
class RepositoryManagerTemplateImporterComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail(false);
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('ContentObjectTemplateImport')));
		$trail->add_help('repository importer');

		$extra_params = array();

		$user = new User();
		$user->set_id(0);
		
		$import_form = new ContentObjectImportForm('import', 'post', $this->get_url($extra_params), 0, $user);

		if ($import_form->validate())
		{
			$content_object = $import_form->import_content_object();

			if ($content_object === false)
			{
				$message = Translation :: get('ContentObjectNotImported');
			}
			else
			{
				$message = Translation :: get('ContentObjectImported');
			}
			
			$this->redirect($message, !isset($content_object), array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_TEMPLATES));
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