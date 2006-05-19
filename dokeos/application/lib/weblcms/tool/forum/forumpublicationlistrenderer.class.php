<?php
/**
 * Forum tool - list renderer
 * @package application.weblcms.tool
 * @subpackage forum
 */
require_once dirname(__FILE__).'/../../browser/list_renderer/tablelearningobjectpublicationlistrenderer.class.php';

class ForumPublicationListRenderer extends TableLearningObjectPublicationListRenderer
{
    function ForumPublicationListRenderer($browser)
    {
    	parent :: __construct($browser);
    	$this->set_header(0, '' , false);
    	$this->set_header(1, get_lang('Forum'), false);
		$this->set_header(2, get_lang('Topics'), false);
		$this->set_header(3, get_lang('Posts'), false);
		$this->set_header(4, get_lang('LastPost'), false);
		$this->set_header(5, '', false);
    }
}
?>