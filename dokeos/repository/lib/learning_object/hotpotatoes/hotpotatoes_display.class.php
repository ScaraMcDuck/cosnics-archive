<?php
/**
 * $Id: announcement_display.class.php 9191 2006-09-01 11:48:41Z bmol $
 * @package repository.learningobject
 * @subpackage exercise
 */
/**
 * This class can be used to display open questions
 */
class HotpotatoesDisplay extends ContentObjectDisplay
{
	function get_full_html()
	{	
		$object = $this->get_content_object();
		$path = Path :: get(WEB_REPO_PATH) . $object->get_path();
		$html = '<iframe src="' . $path . '" width="100%" height="600">
  				 <p>Your browser does not support iframes.</p>
				 </iframe>';
		
		return $html;
	}
	
	//Inherited
	function get_short_html()
	{
		$object = $this->get_content_object();
		return '<span class="content_object"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>