<?php
/**
 * $Id$
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../../metadata/ieee_lom/ieee_lom_generator.class.php';
/**
 * Repository manager component to edit the metadata of a learning object.
 */
class RepositoryManagerMetadataEditorComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => get_lang('EditMetadata')));
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if ($id)
		{
			$object = $this->retrieve_learning_object($id);
			$lom = IeeeLomGenerator::generate($object);
			$this->display_header($breadcrumbs);
			$lom->display();
			$this->display_footer();
		}
		else
		{
			throw new Exception(get_lang('InvalidDataRetrievedFromDatabase'));
		}

	}
}
?>