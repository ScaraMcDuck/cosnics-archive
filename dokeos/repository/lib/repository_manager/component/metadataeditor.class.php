<?php
/**
 * $Id$
 * @package repository.repositorymanager
 * 
 * @author Bart Mollet
 * @author Tim De Pauw
 * @author Hans De Bisschop
 * @author Dieter De Neef
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
		$breadcrumbs = array(array('url' => $this->get_url(), 'name' => Translation :: get('Metadata')));
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if ($id)
		{
			$object = $this->retrieve_learning_object($id);
			$lom = IeeeLomGenerator::generate($object);
			$this->display_header($breadcrumbs);
			echo '<div class="metadata" style="background-image: url('.$this->get_path(WEB_IMG_PATH).'info_small.gif);">';
			echo '<div class="title">'. $object->get_title(). '</div>';
			echo '<pre>';
			$lom->display();
			echo '</pre>';
			echo '</div>';
			$this->display_footer();
		}
		else
		{
			throw new Exception(Translation :: get('InvalidDataRetrievedFromDatabase'));
		}

	}
}
?>