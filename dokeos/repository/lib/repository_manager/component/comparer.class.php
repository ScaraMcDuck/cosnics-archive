<?php
/**
 * @package repository.repositorymanager
 * 
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */
require_once dirname(__FILE__).'/../repository_manager.class.php';
require_once dirname(__FILE__).'/../repository_manager_component.class.php';
require_once dirname(__FILE__).'/../../learning_object_difference_display.class.php';
/**
 * Repository manager component which can be used to compare a learning object.
 */
class RepositoryManagerComparerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		
		$object_id = $_GET[RepositoryManager :: PARAM_COMPARE_OBJECT];
		$version_id = $_GET[RepositoryManager :: PARAM_COMPARE_VERSION];
		
		if ($object_id && $version_id)
		{
			$object = $this->retrieve_learning_object($object_id);
			
			if ($object->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$trail->add(new Breadcrumb($this->get_recycle_bin_url(), Translation :: get('RecycleBin')));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$trail->add(new Breadcrumb(null, $object->get_title() . ($object->is_latest_version() ? '' : ' ('.Translation :: get('OldVersion').')')));
			$trail->add(new Breadcrumb($this->get_url(), Translation :: get('DifferenceBetweenTwoVersions')));
			$this->display_header($trail);
			
			$diff = $object->get_difference($version_id);
			
			$display = LearningObjectDifferenceDisplay :: factory($diff);
			
			echo DokeosUtilities :: add_block_hider();
			echo DokeosUtilities :: build_block_hider('compare_legend');
			echo $display->get_legend();
			echo DokeosUtilities :: build_block_hider();
			echo $display->get_diff_as_html();
			
			$this->display_footer();
		}
		else
		{
			$this->display_warning_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
}
?>