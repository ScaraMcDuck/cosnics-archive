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
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../repository_manager.class.php';
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
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('Metadata')));
		$id = $_GET[RepositoryManager :: PARAM_LEARNING_OBJECT_ID];
		if ($id)
		{
			$object = $this->retrieve_learning_object($id);
			$lom = IeeeLomGenerator::generate($object);
			$this->display_header($trail);
			echo '<div class="metadata" style="background-image: url('.Theme :: get_common_image_path().'place_metadata.png);">';
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