<?php
/**
 * @package repository.learningobject
 * @subpackage document
 */
require_once dirname(__FILE__).'/../../../../claroline/inc/lib/fileDisplay.lib.php';

class DocumentDisplay extends LearningObjectDisplay
{
	function get_full_html()
	{
		$html = parent :: get_full_html();
		$object = $this->get_learning_object();
		return preg_replace('|</div>\s*$|s', '<div class="document_link" style="margin-top: 1em;"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_filename()).'</a> ('.format_file_size($object->get_filesize()).')</div></div>', $html);
	}
	function get_short_html()
	{
		$object = $this->get_learning_object();
		return '<span class="learning_object"><a href="'.htmlentities($object->get_url()).'">'.htmlentities($object->get_title()).'</a></span>';
	}
}
?>