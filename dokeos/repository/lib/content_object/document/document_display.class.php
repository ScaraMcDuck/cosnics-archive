<?php
/**
 * $Id: document_display.class.php 23130 2009-09-25 12:40:53Z vanpouckesven $
 * @package repository.learningobject
 * @subpackage document
 */
/**
 * This class can be used to display documents
 */
class DocumentDisplay extends ContentObjectDisplay
{
	//Inherited
	function get_full_html()
	{	
		$html = parent :: get_full_html();
		$object = $this->get_content_object();
		$name = $object->get_filename();

		$url = RepositoryManager :: get_document_downloader_url($object->get_id());
		
		if(strtolower(substr($name, -3)) == 'jpg' || strtolower(substr($name, -4)) == 'jpeg' || strtolower(substr($name, -3)) == 'bmp' || strtolower(substr($name, -3)) == 'png')
		{
			$html = preg_replace('|</div>\s*$|s', '<a href="'.htmlentities($url).'"><img style="max-width: 100%" src="' . $url . '" /></a></div>' , $html);
		}
		else
		{
			$html = preg_replace('|</div>\s*$|s', '<div class="document_link" style="margin-top: 1em;"><a href="'.htmlentities($url).'">'.htmlentities($name).'</a> ('.Filesystem::format_file_size($object->get_filesize()).')</div></div>', $html);
		}
		
		return $html;
	}
	
	//Inherited
	function get_short_html()
	{
		$object = $this->get_content_object();
		$url = RepositoryManager :: get_document_downloader_url($object->get_id());
		
		return '<span class="content_object"><a href="'.htmlentities($url).'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>