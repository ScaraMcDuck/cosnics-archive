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
		$id1 = $_POST['vgl2'];
		$id2 = $_POST['vgl1'];
		if ($id1 && $id2)
		{
			$object1 = $this->retrieve_learning_object($id1);
			$object2 = $this->retrieve_learning_object($id2);
			
			$string1 = $object1->get_description();
        	$string1 = str_replace('<p>', '', $string1);
        	$string1 = str_replace('</p>', "<br />\n", $string1);
        	$string1 = strip_tags($string1, '<br />');
        	$string1 = explode("\n", $string1);
        	
        	$string2 = $object2->get_description();
        	$string2 = str_replace('<p>', '', $string2);
        	$string2 = str_replace('</p>', "<br />\n", $string2);
        	$string2 = strip_tags($string2, '<br />');
			$string2 = explode("\n", $string2);

			$display = LearningObjectDisplay :: factory($object1);
			$breadcrumbs = array();

			if ($object1->get_state() == LearningObject :: STATE_RECYCLED)
			{
				$breadcrumbs[] = array('url' => $this->get_recycle_bin_url(), 'name' => get_lang('RecycleBin'));
				$this->force_menu_url($this->get_recycle_bin_url());
			}
			$breadcrumbs[] = array('url' => $this->get_url(), 'name' => $object1->get_title() . ($object1->is_latest_version() ? '' : ' ('.get_lang('Compared').')'));
			$this->display_header($breadcrumbs);
			
			$de = new Text_Diff($string1, $string2);
			
			$diff = $de->getDiff();
			
			
			echo '<table>';
			echo $this->parse($diff);
			echo '</table>';
			
						
			$this->display_footer();
		}
		else
		{
			$this->display_error_page(htmlentities(get_lang('NoObjectSelected')));
		}
	}
	
function parse($diff)
{
	$html = array();
	foreach($diff as $d)
 	{
		$html[] = '<tr>';
		$html[] = '<td width="200px">';
		$html[] = print_r($d->parse('final'), true) . '';
		$html[] = '</td>';
		$html[] = '<td width="50px"></td>';
		$html[] = '<td width="200px">';
		$html[] = print_r($d->parse('orig'), true) . '';
		$html[] = '</td>';
		$html[] = '</tr>';
	}
	return implode($html);
}
}
?>