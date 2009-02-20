<?php
/**
 * $Id$
 * @package repository.learningobject
 * @subpackage document
 */
/**
 * This class can be used to display documents
 */
class DocumentDisplay extends LearningObjectDisplay
{
	//Inherited
	function get_full_html()
	{	
		$html = parent :: get_full_html();
		$object = $this->get_learning_object();
		$name = $object->get_filename();
		if(substr($name, -3) == 'jpg' || substr($name, -4) == 'jpeg' || substr($name, -3) == 'bmp' || substr($name, -3) == 'png')
			$html = preg_replace('|</div>\s*$|s', '<a href="'.htmlentities($object->get_url()).'"><img style="max-width: 100%" src="' . $object->get_url() . '" /></a></div>' , $html);
		else
			$html = preg_replace('|</div>\s*$|s', '<div class="document_link" style="margin-top: 1em;"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_filename()).'</a> ('.Filesystem::format_file_size($object->get_filesize()).')</div></div>', $html);
		
		return $html;
	}
	//Inherited
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>