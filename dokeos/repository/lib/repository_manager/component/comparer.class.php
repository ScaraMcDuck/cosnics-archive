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
		$id1 = $_GET['object'];
		$id2 = $_GET['compare'];
		if ($id1 && $id2)
		{
			$object1 = $this->retrieve_learning_object($id1);
			$object2 = $this->retrieve_learning_object($id2);
			
			$string1 = $object1->get_description();
        	$string1 = str_replace('<p>', '', $string1);
        	$string1 = str_replace('</p>', "<br />\n", $string1);
        	$string1 = explode("\n", $string1);
        	
        	$string2 = $object2->get_description();
        	$string2 = str_replace('<p>', '', $string2);
        	$string2 = str_replace('</p>', "<br />\n", $string2);
			$string2 = explode("\n", $string2);

			$display = LearningObjectDisplay :: factory($object1);
			$breadcrumbs = array();

			if ($object1->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => get_lang('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => get_lang('DifferenceBetweenTwoVersions'));
			$this->display_header($breadcrumbs);

			$de = new Text_Diff($string1, $string2);
			
			$diff = $de->getDiff();
			
			$html = array();
			$html[] = '<div class="difference" style="background-image: url('.api_get_path(WEB_CODE_PATH).'img/'.$object1->get_icon_name().'.gif);">';			
			$html[] = '<div class="titleleft">';
			$html[] = $object2->get_title();
			$html[] = date(" (d M Y, H:i:s O)",$object2->get_modification_date());
			$html[] = '</div>';
			$html[] = '<div class="titleright">';
			$html[] = $object1->get_title();
			$html[] = date(" (d M Y, H:i:s O)",$object1->get_modification_date());
			$html[] = '</div>';
			foreach($diff as $d)
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