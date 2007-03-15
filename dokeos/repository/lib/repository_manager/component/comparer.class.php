<?php
/**
 * @package repository.repositorymanager
 */
require_once dirname(__FILE__).'/../repositorymanager.class.php';
require_once dirname(__FILE__).'/../repositorymanagercomponent.class.php';
require_once dirname(__FILE__).'/../../learningobjectdisplay.class.php';
require_once dirname(__FILE__).'/../../differenceengine.class.php';
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
			$version = $this->retrieve_learning_object($version_id);

			$breadcrumbs = array();

			if ($object->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => get_lang('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => get_lang('DifferenceBetweenTwoVersions'));
			$this->display_header($breadcrumbs);
			
			$diff = $object->get_difference($version_id);
			
			$html = array();
			$html[] = '<div class="difference" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$object->get_icon_name().'.gif);">';			
			$html[] = '<div class="titleleft">';
			$html[] = $diff->get_object_title();
			$html[] = date(" (d M Y, H:i:s O)",$version->get_modification_date());
			$html[] = '</div>';
			$html[] = '<div class="titleright">';
			$html[] = $diff->get_version_title();
			$html[] = date(" (d M Y, H:i:s O)",$object->get_modification_date());
			$html[] = '</div>';
			foreach($diff->get_difference() as $d)
 			{

				$html[] = '<div class="left">';
				$html[] = print_r($d->parse('final'), true) . '';
				$html[] = '</div>';
				$html[] = '<div class="right">';
				$html[] = print_r($d->parse('orig'), true) . '';
				$html[] = '</div>';
				$html[] = '<br style="clear:both;" />';
				
			}
			$html[] = '</div>';
			echo implode($html);
			
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
	

}
?>