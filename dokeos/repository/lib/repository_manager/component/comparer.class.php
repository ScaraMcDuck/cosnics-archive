<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectdifferencedisplay.class.php';
/**
 * Repository manager component which can be used to view a learning object.
 */
class RepositoryManagerComparerComponent extends RepositoryManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$object_id = $_GET[RepositoryManager :: PARAM_COMPARE_OBJECT];
		$version_id = $_GET[RepositoryManager :: PARAM_COMPARE_VERSION];
		
		if ($object_id && $version_id)
		{
			$object = $this->retrieve_learning_object($object_id);

			$breadcrumbs = array();

			$breadcrumbs = array();
			if ($object->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => get_lang('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('name' => $object->get_title() . ($object->is_latest_version() ? '' : ' ('.get_lang('OldVersion').')'));
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => get_lang('DifferenceBetweenTwoVersions'));
			$this->display_header($breadcrumbs);
			
			$diff = $object->get_difference($version_id);
			
			$display = LearningObjectDifferenceDisplay :: factory($diff);
			
			echo RepositoryUtilities :: build_block_hider('script');
			echo RepositoryUtilities :: build_block_hider('begin', 'cole', 'CompareLegend');
			echo $display->get_legend();
			echo RepositoryUtilities :: build_block_hider('end', 'cole');
			echo $display->get_diff_as_html();
			
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
	

}
?>